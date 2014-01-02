<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\HandlebarsEngine;

class WebsiteExamplesTest extends \unittest\TestCase {

  /**
   * Render a template with a list of variables
   *
   * @param  string $template
   * @param  [:var] $variables
   * @param  [:var] $helpers
   * @return string
   */
  protected function render($template, $variables, $helpers= array()) {
    return create(new HandlebarsEngine())->withHelpers($helpers)->render($template, $variables);
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
        array(
          'title' => 'My New Post',
          'body'  => 'This is my first post!'
        )
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
        array(
          'title' => 'All About <p> Tags',
          'body'  => '<p>This is a post about &lt;p&gt; tags</p>'
        )
      )
    );
  }

  #[@test]
  public function block_expressions() {
    $this->assertEquals(
      "<ul>\n".
      "  <li>Yehuda Katz</li>\n".
      "  <li>Carl Lerche</li>\n".
      "  <li>Alan Johnson</li>\n".
      "</ul>",
      $this->render(
        "{{#list people}}{{firstName}} {{lastName}}{{/list}}",
        array('people' => array(
          array('firstName' => 'Yehuda', 'lastName' => 'Katz'),
          array('firstName' => 'Carl', 'lastName' => 'Lerche'),
          array('firstName' => 'Alan', 'lastName' => 'Johnson')
        )),
        array('list' => function($items, $context, $options) {
          $list= $context->lookup($options[0]);
          if ($context->isList($list)) {
            $out= "<ul>\n";
            foreach ($list as $element) {
              $out.= '  <li>'.$items->evaluate($context->asContext($element))."</li>\n";
            }
            return $out.'</ul>';
          } else {
            return '';
          }
        })
      )
    );
  }

  #[@test]
  public function nested_paths() {
    $this->assertEquals(
      "<div class=\"entry\">\n".
      "  <h1>My First Blog Post!</h1>\n".
      "  <h2>By Yehuda Katz</h2>\n".
      "\n".
      "  <div class=\"body\">\n".
      "    My first post. Wheeeee!\n".
      "  </div>\n".
      "</div>\n",
      $this->render(
        "<div class=\"entry\">\n".
        "  <h1>{{title}}</h1>\n".
        "  <h2>By {{author.name}}</h2>\n".
        "\n".
        "  <div class=\"body\">\n".
        "    {{body}}\n".
        "  </div>\n".
        "</div>\n",
        array(
          'title'  => 'My First Blog Post!',
          'author' => array(
            'id'   => 47,
            'name' => 'Yehuda Katz'
          ),
          'body'   => 'My first post. Wheeeee!'
        )
      )
    );
  }

  #[@test]
  public function dot_dot_segments() {
    $this->assertEquals(
      "<h1>Comments</h1>\n".
      "\n".
      "<div id=\"comments\">\n".
      "  <h2><a href=\"/posts/42-the-answer#1\">But...</a></h2>\n".
      "  <div>...what was the question?</div>\n".
      "</div>\n",
      $this->render(
        "<h1>Comments</h1>\n".
        "\n".
        "<div id=\"comments\">\n".
        "  {{#each comments}}\n".
        "  <h2><a href=\"/posts/{{../permalink}}#{{id}}\">{{title}}</a></h2>\n".
        "  <div>{{body}}</div>\n".
        "  {{/each}}\n".
        "</div>\n",
        array(
          'permalink' => '42-the-answer',
          'comments'  => array(array(
            'id'    => 1,
            'title' => 'But...',
            'body'  => '...what was the question?'
          ))
        )
      )
    );
  }

  #[@test]
  public function unless_with_license_as_first_option() {
    $this->assertEquals(
      "<div class=\"entry\">\n".
      "<h3 class=\"warning\">WARNING: This entry does not have a license!</h3>\n".
      "</div>\n",
      $this->render(
        "<div class=\"entry\">\n".
        "{{#unless license}}\n".
        "<h3 class=\"warning\">WARNING: This entry does not have a license!</h3>\n".
        "{{/unless}}\n".
        "</div>\n",
        array('license' => null)
      )
    );
  }

  #[@test]
  public function the_each_block_helper() {
    $this->assertEquals(
      "<ul class=\"people_list\">\n".
      "  <li>Yehuda Katz</li>\n".
      "  <li>Carl Lerche</li>\n".
      "  <li>Alan Johnson</li>\n".
      "</ul>",
      $this->render(
        "<ul class=\"people_list\">\n".
        "  {{#each people}}\n".
        "  <li>{{this}}</li>\n".
        "  {{/each}}\n".
        "</ul>",
        array('people' => array(
          array('Yehuda Katz'),
          array('Carl Lerche'),
          array('Alan Johnson')
        ))
      )
    );
  }
}