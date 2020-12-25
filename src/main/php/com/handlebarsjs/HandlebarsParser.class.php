<?php namespace com\handlebarsjs;

use com\github\mustache\{
  AbstractMustacheParser,
  CommentNode,
  IteratorNode,
  VariableNode,
  TextNode,
  TemplateFormatException,
  ParseState
};
use lang\MethodNotImplementedException;
use text\Tokenizer;

/**
 * Parses handlebars templates
 *
 * @test  xp://com.handlebarsjs.unittest.ParsingTest
 * @test  xp://com.handlebarsjs.unittest.SubexpressionsTest
 * @see   https://github.com/wycats/handlebars.js/blob/master/spec/parser.js
 */
class HandlebarsParser extends AbstractMustacheParser {

  /**
   * Tokenize name and options from a given tag, e.g.:
   * - 'tag' = ['tag']
   * - 'tag option "option 2"' = ['tag', 'option', 'option 2']
   * - 'tag option key=value' = ['tag', 'option', 'key' => 'value']
   *
   * @param  string $tag
   * @return string[]
   */
  public function options($tag) {
    $o= strcspn($tag, "\r\n\t ");
    $parsed= [substr($tag, 0, $o)];
    $key= null;
    for ($o++, $l= strlen($tag); $o < $l; $o+= $p + 1) {
      if ('"' === $tag[$o] || "'" === $tag[$o]) {           // Single and double quoted strings
        $chars= '';
        while ($o < $l) {
          $p= strcspn($tag, $tag[$o], $o + 1) + 2;
          $chars.= substr($tag, $o + 1, $p - 2);
          $c= strlen($chars);
          if ($c > 0 && '\\' === $chars[$c - 1]) {
            $chars= substr($chars, 0, -1).$tag[$o];
            $o+= $p - 1;
            continue;
          }
          break;
        }
        $value= new Quoted($chars);
      } else if ('(' === $tag[$o]) {                        // Subexpressions (+nesting!)
        $s= $o;
        $b= 0;
        do {
          $p= strcspn($tag, '()', $o);
          if ('(' === $tag[$o + $p]) $b++;
          if (')' === $tag[$o + $p]) $b--;
          $o+= $p + 1;
        } while ($o < $l && $b);
        $p= 0;
        $sub= $this->options(substr($tag, $s + 1, $o - $s - 2));
        $value= new Expression(array_shift($sub), $sub);
      } else if (' ' === $tag[$o] || "\t" === $tag[$o] || "\r" === $tag[$o] || "\n" === $tag[$o]) {
        $p= strcspn($tag, "\r\n\t ", $o);
        continue;
      } else {
        $p= strcspn($tag, ' =', $o);
        if ($o + $p < $l && '=' === $tag[$o + $p]) {
          $key= substr($tag, $o, $p);
          continue;
        } else {
          $token= substr($tag, $o, $p);
          if ('true' === $token) {
            $value= new Constant(true);
          } else if ('false' === $token) {
            $value= new Constant(false);
          } else if ('null' === $token) {
            $value= new Constant(null);
          } else if ('.' === $token) {
            $value= new Lookup(null);
          } else if (strspn($token, '-.0123456789') === strlen($token)) {
            $value= new Constant(strstr($token, '.') ? (double)$token : (int)$token);
          } else {
            $value= new Lookup($token);
          }
        }
      }
      if ($key) {
        $parsed[$key]= $value;
        $key= null;
      } else {
        $parsed[]= $value;
      }
    }
    return $parsed;
  }

  /**
   * Initialize this parser.
   */
  protected function initialize() {

    // Sections
    $this->withHandler('#', true, function($tag, $state, $parse) {
      $parsed= $parse->options(trim(substr($tag, 1)));
      $name= array_shift($parsed);
      $state->parents[]= $state->target;

      if ('*' === $name[0]) {
        $block= $state->target->decorate(new Decoration($name, $parsed));
      } else {
        $block= $state->target->add(BlockHelpers::newInstance(
          $name,
          $parsed,
          null,
          null,
          $state->start,
          $state->end
        ));
      }
      $state->parents[]= $block;
      $state->target= $block->fn();
    });
    $this->withHandler('/', true, function($tag, $state) {
      $name= trim(substr($tag, 1));
      $block= array_pop($state->parents);
      if (null === $block) {
        throw new TemplateFormatException('Illegal nesting, no start tag, but have {{/'.$name.'}}');
      } else if ($name !== $block->name()) {
        throw new TemplateFormatException('Illegal nesting, expected {{/'.$block->name().'}}, have {{/'.$name.'}}');
      }
      $state->target= array_pop($state->parents);
    });

    // > partial
    $this->withHandler('>', true, function($tag, $state, $parse) {
      $options= $parse->options(trim($tag));
      array_shift($options);
      $template= array_shift($options);
      if ($template instanceof Lookup) {
        $template= new Constant((string)$template);
      }
      $state->target->add(new PartialNode($template, $options, $state->padding));
    });

    // ! ... for comments
    $this->withHandler('!', true, function($tag, $state) {
      $state->target->add(new CommentNode(trim(substr($tag, 1), ' ')));
    });

    // Change start and end
    $this->withHandler('=', true, function($tag, $state) {
      list($state->start, $state->end)= explode(' ', trim(substr($tag, 1, -1)));
    });

    // & for unescaped
    $this->withHandler('&', false, function($tag, $state, $parse) {
      $parsed= $parse->options(trim(substr($tag, 1)));
      $state->target->add('.' === $parsed[0]
        ? new IteratorNode(false)
        : new VariableNode($parsed[0], false, array_slice($parsed, 1))
      );
    });

    // triple mustache for unescaped, quadruple for raw
    $this->withHandler('{', false, function($tag, $state, $parse) {
      $parsed= $parse->options(trim(substr($tag, 1)));
      $state->target->add('.' === $parsed[0]
        ? new IteratorNode(false)
        : new VariableNode($parsed[0], false, array_slice($parsed, 1))
      );
      return +1; // Skip "}"
    });

    // ^ is either an else by its own, or a negated block
    $this->withHandler('^', true, function($tag, $state) {
      if ('^' === trim($tag)) {
        $block= cast($state->parents[sizeof($state->parents) - 1], 'com.handlebarsjs.BlockNode');
        $state->target= $block->inverse();
        return;
      }
      throw new MethodNotImplementedException('^blocks not yet implemented');
    });

    // Default
    $this->withHandler(null, false, function($tag, $state, $parse) {
      $parsed= $parse->options(trim($tag));
      if ('.' === $parsed[0]) {
        $state->target->add(new IteratorNode(true));
        return;
      } else if ('else' === $parsed[0] && $state->parents) {
        $context= $state->parents[sizeof($state->parents) - 1];
        if ($context instanceof BlockNode) {
          $state->target= $context->inverse();
          return;
        }
        // Fall through, "else" has no special meaning here.
      }
      $state->target->add(new VariableNode($parsed[0], true, array_slice($parsed, 1)));
    });
  }

  /**
   * Parse a template
   *
   * @param  string $template The template as a string
   * @param  string $start Initial start tag, defaults to "{{"
   * @param  string $end Initial end tag, defaults to "}}"
   * @param  string $indent What to prefix before each line
   * @return com.github.mustache.Node The parsed template
   * @throws com.github.mustache.TemplateFormatException
   */
  public function parse(Tokenizer $tokens, $start= '{{', $end= '}}', $indent= '') {
    $state= new ParseState();
    $state->target= new Nodes();
    $state->start= $start;
    $state->end= $end;
    $state->parents= [];
    $standalone= implode('', array_keys($this->standalone));

    // Tokenize template
    $tokens->delimiters= "\n";
    $tokens->returnDelims= true;
    while ($tokens->hasMoreTokens()) {
      $token= $tokens->nextToken();

      // Yield empty lines as separate text nodes
      if ("\n" === $token) {
        $state->target->add(new TextNode($indent.$token));
        continue;
      }

      $line= $indent.$token.$tokens->nextToken();
      $offset= 0;
      $length= strlen($line);
      do {
        $state->padding= '';

        // Find first unescaped `{{` starting at $offset
        $o= $offset;
        do {
          $s= strpos($line, $state->start, $o);
          $o= $s + 1;
        } while ($s > 0 && '\\' === $line[$s - 1]);

        if (false === $s) {
          $text= str_replace('\\{{', '{{', substr($line, $offset));
          $tag= null;
          $offset= $length;
        } else {
          while (false === ($e= strpos($line, $state->end, $s + strlen($state->start)))) {
            if (!$tokens->hasMoreTokens()) {
              throw new TemplateFormatException('Unclosed '.$state->start.', expecting '.$state->end);
            }
            $line.= $indent.$tokens->nextToken().$tokens->nextToken();
          }
          $text= str_replace('\\{{', '{{', substr($line, $offset, $s - $offset));
          $tag= substr($line, $s + strlen($state->start), $e - $s - strlen($state->end));
          $offset= $e + strlen($state->end);

          // Check for standalone tags on a line by themselves
          if (0 === strcspn($tag, $standalone)) {
            if ('' === trim(substr($line, 0, $s).substr($line, $offset))) {
              $offset= $length;
              $state->padding= substr($line, 0, $s);
              $text= '';
            }
          }
        }

        // Handle text
        if ('' !== $text) {
          $state->target->add(new TextNode($text));
        }

        // Handle tag
        if (null === $tag) {
          continue;
        } else if (isset($this->handlers[$tag[0]])) {
          $f= $this->handlers[$tag[0]];
        } else {
          $f= $this->handlers[null];
        }
        $offset+= $f($tag, $state, $this);
      } while ($offset < $length);
    }

    // Check for unclosed sections
    if (!empty($state->parents)) {
      throw new TemplateFormatException('Unclosed section '.$state->target->name());
    }

    return $state->target;
  }
}