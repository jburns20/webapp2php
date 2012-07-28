<?php

class HomeHandler extends Handler {
	public function get() {
		$this->render_write_file("hello.twig", array("name" => "World"));
	}
}