webapp2php
==========

A web framework that attempts to bring the simplicity and flexibility of Python's webapp2 to PHP. A simple homepage only requires the following code:
```
class HomeHandler extends Handler {
	public function get() {
		$this->render_write_file("home.twig", array("name" => "World"));
	}
}
```

Includes [Twig](http://twig.sensiolabs.org) as a template engine and [RedBeanPHP](http://www.redbeanphp) as an ORM.
