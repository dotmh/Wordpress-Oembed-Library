DOT MH OEMBED WORDPRESS LIBRARY
===============================

A library to be used within plugins or themes to allow easier access to an oembed endpoint 
please see http://oembed.com/ for the oembed specification 

In all examples the var $embed is an instance of this class

PLEASE NOTE : This is NOT a wordpress plugin.

Usage 
-----

###Setting the data using the constuctor

```php

	$request = array( 'endpoint' => 'http://someendpoint.com',
					  'url' => 'http://someresource.com',	
					  'maxwidth' => '800px',
					  'maxheight' => '600px'
	);	

	try {
		$embed = new dotmh_embed( $request , TRUE);
	}
	catch( Exception $e ) {
		/* Handle Error */
	}
```

###Setting the data using the properties

```php

	$embed = new dotmh_embed();

	$embed->endpoint = 'http://someendpoint.com';
	$embed->url = 'http://someresource.com';
	$embed->maxwidth = '800px';
	$embed->maxheight => '600px';

	try {
		$embed->load();
	}
	catch( Exception $e ) {
		/* Handle Error */
	}

```

###You can also set the data using the construct but manually load the response.

	$request = array( 'endpoint' => 'http://someendpoint.com',
					  'url' => 'http://someresource.com',	
					  'maxwidth' => '800px',
					  'maxheight' => '600px'
	);	

	$embed = new dotmh_embed( $request );

	try {
		$embed->load();	
	}
	catch( Exception $e ) {
		/* Handle Error */
	}

###Getting to the response data 

The response data is stored in the object as properties, so for example to get the http field you would do 

```php
	
	$html = $embed->html;

```

The actual data that is returned depends on the resource that you requested to embed, you can test if you got a certain field back using the following , as if a field doesn't exist it will return false when called

```php

	if ( $html = $embed->html ) {
		/* Do something */
	}
```

for more information on the responses again see http://oembed.com/.

Please note : Open graph properties must be retrived using camel case for example

to get og:description use 

```php
	$embed->ogDescription
```

Exceptions
----------

This class is written in an OOP way so that it throws exceptions on errors , this makes it more flexible because you can handle these as you see fit. If the error returned was an http error then the http error code with be the php exception code and the http message will be the php message. 

The class will only throw an exception when the load() method is called , including when its called automatically by the construct when the $autoload parameter is set to true. 

Notes
-----

This class can at current ONLY handle oembed endpoints that respond in JSON NOT XML 
This class has NOT been production tested 

Other than that Have Fun! 

License
--------

Copyright (c) <2012> <DotMH>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE