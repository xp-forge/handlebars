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
 * @test  xp://com.github.mustache.unittest.ParsingTest
 */
class HandlebarsParser extends AbstractMustacheParser {

  /**
   * Initialize this parser.
   */
  protected function initialize() {

    // Sections
    $this->withHandler('#^', true, function($tag, $state, $options) {
      $parsed= $options(trim(substr($tag, 1)));
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
    $this->withHandler('&', false, function($tag, $state, $options) {
      $parsed= $options(trim(substr($tag, 1)));
      $state->target->add('.' === $parsed[0]
        ? new IteratorNode(false)
        : new VariableNode($parsed[0], false, array_slice($parsed, 1))
      );
    });

    // triple mustache for unescaped
    $this->withHandler('{', false, function($tag, $state, $options) {
      $parsed= $options(trim(substr($tag, 1)));
      $state->target->add('.' === $parsed[0]
        ? new IteratorNode(false)
        : new VariableNode($parsed[0], false, array_slice($parsed, 1))
      );
      return +1; // Skip "}"
    });

    // Default
    $this->withHandler(null, false, function($tag, $state, $options) {
      $parsed= $options(trim($tag));
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