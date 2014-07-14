<?php

class HomeHandler extends Handler {
	public function get() {
		/*$test = R::dispense("test");
		$test->name = "World";
		R::store($test);*/
		$obj = R::findOne("test", " name = 'World' ");
		$this->render_write_file("hello.twig", array("name" => $obj->name));
	}
}