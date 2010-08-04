<?php
App::import('Model', 'AppModel');

class Tag extends AppModel {
	var $name = 'Tag';

  var $hasAndBelongsToMany = array('Post' => array('joinTable' => 'posts_tags',
                                                   'associationForeignKey' => 'post_id'));
}

class Category extends AppModel {
	var $name = 'Category';

  var $hasMany = array('Post');
}

class Post extends AppModel {
	var $name = 'Post';

  var $hasAndBelongsToMany = array('Tag' => array('joinTable' => 'posts_tags',
                                                  'associationForeignKey' => 'tag_id'));
  var $belongsTo = array('Category');
}

class AppModelTestCase extends CakeTestCase {
  var $Post = null;
  var $fixtures = array('post',
                        'tag',
                        'category',
                        'posts_tag');

  function startTest() {
    $this->Post = & ClassRegistry::init('Post');
  }

  function endTest() {
    unset($this->Post);
	ClassRegistry::flush();
  }

  function testPostInstance() {
    $this->assertTrue(is_a($this->Post, 'Post'));
  }

  function testNoAssociations() {
    $this->assertFalse(property_exists($this->Post, 'Category'));
    $this->assertFalse(property_exists($this->Post, 'Tag'));
  }

  function testNoAssociationsHabtm() {
    $this->assertFalse(property_exists($this->Post, 'Tag'));
  }

  function testLazyBinding() {
    $this->assertFalse(property_exists($this->Post, 'Category'));
    $this->Post->Category;
    $this->assertTrue(property_exists($this->Post, 'Category'));
    $this->assertTrue(is_a($this->Post->Category, 'Category'));
  }

  function testLazyBindingHabtm() {
    $this->assertFalse(property_exists($this->Post, 'Tag'));
    $this->Post->Tag;
    $this->assertTrue(property_exists($this->Post, 'Tag'));
    $this->assertTrue(is_a($this->Post->Tag, 'Tag'));
    $this->assertTrue(property_exists($this->Post, 'PostsTag'));
    $this->assertTrue(is_a($this->Post->PostsTag, 'AppModel'));
  }

  function testContain() {
	$this->Post->Behaviors->attach('Containable');
    $this->assertFalse(property_exists($this->Post, 'Category'));
    $results = $this->Post->find('all',array('contain' => false));
    $this->assertFalse(property_exists($this->Post, 'Category'));
    $this->assertEqual(array('Post'), array_keys($results[0]));
  }

  function testContainLazyBinding() {
    $this->Post->Behaviors->attach('Containable');
    $this->assertFalse(property_exists($this->Post, 'Category'));
    $this->Post->contain('Category');
    $this->assertFalse(property_exists($this->Post, 'Category'));
    $results = $this->Post->find('all');
    $this->assertTrue(property_exists($this->Post, 'Category'));
    $this->assertTrue(is_a($this->Post->Category, 'Category'));
    $this->assertEqual(array('Post', 'Category'), array_keys($results[0]));
  }

  function testContainLazyBindingResetFalse() {
    $this->Post->Behaviors->attach('Containable');
    $this->Post->contain(array('Category', 'Tag'), false);
    $results = $this->Post->find('all');
    $this->assertEqual(array('Post', 'Category', 'Tag'), array_keys($results[0]));
  }

  function testContainLazyBindingHabtm() {
    $this->Post->Behaviors->attach('Containable');
    $this->assertFalse(property_exists($this->Post, 'Tag'));
    $this->Post->contain('Tag');
    $this->assertFalse(property_exists($this->Post, 'Tag'));
    $results = $this->Post->find('all');
    $this->assertTrue(property_exists($this->Post, 'Tag'));
    $this->assertTrue(is_a($this->Post->Tag, 'Tag'));
    $this->assertEqual(array('Post', 'Tag'), array_keys($results[0]));
  }
}