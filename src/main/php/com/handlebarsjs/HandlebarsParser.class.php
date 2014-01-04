<?php namespace com\handlebarsjs;

use com\github\mustache\AbstractMustacheParser;
use com\github\mustache\TemplateFormatException;
use com\github\mustache\PartialNode;
use com\github\mustache\CommentNode;
use com\github\mustache\IteratorNode;
use com\github\mustache\VariableNode;

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
    $o= strcspn($tag, ' ');
    $parsed= array(substr($tag, 0, $o));
    $key= null;
    for ($o++, $l= strlen($tag); $o < $l; $o+= $p + 1) {
      if ('"' === $tag{$o} || "'" === $tag{$o}) {           // Single and double quoted strings
        $chars= '';
        while ($o < $l) {
          $p= strcspn($tag, $tag{$o}, $o + 1) + 2;
          $chars.= substr($tag, $o + 1, $p - 2);
          if ('\\' === $chars{strlen($chars) - 1}) {
            $chars= substr($chars, 0, -1).$tag{$o};
            $o+= $p - 1;
            continue;
          }
          break;
        }
        $value= new String($chars);
      } else if ('(' === $tag{$o}) {                        // Subexpressions (+nesting!)
        $s= $o;
        $b= 0;
        do {
          $p= strcspn($tag, '()', $o);
          if ('(' === $tag{$o + $p}) $b++;
          if (')' === $tag{$o + $p}) $b--;
          $o+= $p + 1;
        } while ($o < $l && $b);
        $p= 0;
        $sub= $this->options(substr($tag, $s + 1, $o - $s - 2));
        $value= new Expression(array_shift($sub), $sub);
      } else {
        $p= strcspn($tag, ' =', $o);
        if ($o + $p < $l && '=' === $tag{$o + $p}) {
          $key= substr($tag, $o, $p);
          continue;
        } else {
          $token= substr($tag, $o, $p);
          if ('true' === $token) {
            $value= new Boolean(true);
          } else if ('false' === $token) {
            $value= new Boolean(false);
          } else if (strspn($token, '0123456789') === strlen($token)) {
            $value= new Integer($token);
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
      $state->parents[]= $state->target;
      $block= $state->target->add(BlockHelpers::newInstance(
        array_shift($parsed),
        $parsed,
        null,
        null,
        $state->start,
        $state->end
      ));
      $state->target= $block->fn();
      $state->parents[]= $block;
    });
    $this->withHandler('/', true, function($tag, $state) {
      $name= trim(substr($tag, 1));
      $block= array_pop($state->parents);
      if ($name !== $block->name()) {
        throw new TemplateFormatException('Illegal nesting, expected /'.$state->target->name().', have /'.$name);
      }
      $state->target= array_pop($state->parents);
    });

    // > partial
    $this->withHandler('>', true, function($tag, $state) {
      $state->target->add(new PartialNode(trim(substr($tag, 1), ' '), $state->padding));
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

    // triple mustache for unescaped
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
      raise('lang.MethodNotImplementedException', '^blocks not yet implemented');
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
}