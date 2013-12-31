<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\HandlebarsEngine;

class WebsiteExamplesTest extends \unittest\TestCase {

  /**
   * Render a template with a list of variables
   *
   * @param  string $template
   * @param  [:var] $variables
   * @return string
   */
  protected function render($template, $variables= array()) {
    return create(new HandlebarsEngine())->render($template, $variables);
  }

  #[@test]
  public function getting_started() {
    $this->assertEquals(
      "<div class=\"entry\">\n".
      "  <h1>My New Post</h1>\n".
      "  <div class=\"body\">\n".
      "    This is my first post!\n".
      "  </div>\n".
      "</div>\n",
      $this->render(
        "<div class=\"entry\">\n".
        "  <h1>{{title}}</h1>\n".
        "  <div class=\"body\">\n".
        "    {{body}}\n".
        "  </div>\n".
        "</div>\n",
        array('title' => 'My New Post', 'body' => 'This is my first post!')
      )
    );
  }

  #[@test]
  public function triple_stash() {
    $this->assertEquals(
      "<div class=\"entry\">\n".
      "  <h1>All About &lt;p&gt; Tags</h1>\n".
      "  <div class=\"body\">\n".
      "    <p>This is a post about &lt;p&gt; tags</p>\n".
      "  </div>\n".
      "</div>\n",
      $this->render(
        "<div class=\"entry\">\n".
        "  <h1>{{title}}</h1>\n".
        "  <div class=\"body\">\n".
        "    {{{body}}}\n".
        "  </div>\n".
        "</div>\n",
        array('title' => 'All About <p> Tags', 'body' => '<p>This is a post about &lt;p&gt; tags</p>')
      )
    );
  }
}