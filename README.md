webapp2php
==========

A web framework that attempts to bring the simplicity and flexibility of Python's webapp2 to PHP. A simple homepage only requires the following code:
```
class HomeHandler extends Handler {
	public function get() {
		$this->render_write_file("hello.twig", array("name" => "World"));
	}
}
```

Uses [Twig](http://twig.sensiolabs.org) as a template engine and [RedBeanPHP](http://www.redbeanphp) as an ORM.

Licensed under the New BSD License.

##Setup
Copy all the files into your web root (usually this is the `public_html` directory). Make sure the .htaccess files are copied properly. Then run the following command:
```
php composer.phar install
```
Add the MySQL database, username, and password in `config/config.php` to configure the database connection.
That's it! Open a web browser to test that everything is working.

