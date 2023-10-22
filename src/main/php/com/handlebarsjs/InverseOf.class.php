<?php namespace com\handlebarsjs;

/** @test com.handlebarsjs.unittest.InverseOfTest */
class InverseOf extends BlockNode {

  /** Creates the inverse of a given block */
  public function __construct(parent $block) {
    parent::__construct(
      $block->name,
      $block->options,
      /* fn: */ $block->inverse,
      /* inverse: */ $block->fn,
      $block->start,
      $block->end
    );
  }
}