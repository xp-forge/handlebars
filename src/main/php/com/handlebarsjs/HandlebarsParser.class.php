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
      if ('"' === $tag{$o} || "'" === $tag{$o}) {
        $value= '';
        while ($o < $l) {
          $p= strcspn($tag, $tag{$o}, $o + 1) + 2;
          $value.= substr($tag, $o + 1, $p - 2);
          if ('\\' === $value{strlen($value) - 1}) {
            $value= substr($value, 0, -1).$tag{$o};
            $o+= $p - 1;
            continue;
          }
          break;
        }
      } else if ('(' === $tag{$o}) {
        $p= strcspn($tag, ')', $o);
        $value= new Expression(substr($tag, $o + 1, $p - 1));
      } else {
        $p= strcspn($tag, ' =', $o);
        if ($o + $p < $l && '=' === $tag{$o + $p}) {
          $key= substr($tag, $o, $p);
          continue;
        } else {
          $value= new Lookup(substr($tag, $o, $p));
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
      $block= $state->target->add(new BlockNode(
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