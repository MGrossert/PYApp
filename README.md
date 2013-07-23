PYApp
=====

PY Application Framework



concept
-------
system singleton -> as backend or frontend object
	-> internal cache 
	-> autoloader with multiple paths 
	-> a primary application (multiple applications possible?)
	=> template engine 
		-> function encased html/js/... file with php code
		-> simple backend configuration
	=> data container objects
		-> contains db structure
		-> provides a backend management view
		-> provides a frontend model extendend by the dco
		-> provides easy insertations with safety policies 
		=> database semi-singleton 
			-> one object per server/login
			=> database software based query builder 
				-> should be static or singleton?
	=> modules
		-> modules are one or more of the following types
			- librarys/frameworks/includes
			- application (allow multiple?)
			- content elements
			- backend improvements
		-> can use hooks
		=>
			

requirements
------------
PHP 5.4 -> because traits
up-to-date browser -> for a fast and easy backend 
a  PDO compatible database -> sql syntax only, at first