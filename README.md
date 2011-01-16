# About

This Kohana module provides simple migrations from the command line for SQL compliant databases.

# Warning
Migration module depends on module Console, so you must first install and connect the module Console
https://github.com/ukko/kohana-3-console

# Using

Create a folder named "migrations" in your application folder.  This directory must be writable, or must contain a directory called ".info" which is writable.

Put valid SQL files in that folder, following the naming patterns: 001.sql, 002.sql, etc.

For example:

	001.sql
	002.sql
	003.sql
	004.sql

Now add this module to your application/bootstrap.php file.

    'migrations'    => MODPATH . 'migrations',

Then you can run them from the command line:

## State

	cd /www/kohana
	./cli migrations
    or
    ./cli migrations/index  
   
    ============================ [ Kohana Migrations ] =============================
	    Current:		7
	    Latest:			7
    ================================================================================

## Status full

    ./cli migrations/status
    ============================ [ Kohana Migrations ] =============================

    1	Initial struct DB
    2	Fixtures
    3*	Table `price` add field `spec`
    4	Table `credit` add field `accepted`
    5	Table `providers` add fields `payment` и `garant`
    6	Table `good_price` add field `availability`
    7	Table `provider_store` add field `image`

    ================================================================================
	    Current:		0
	    Latest:			7
    ================================================================================

## Up

	./cli migrations/up/7
	or
	./cli migrations/up/all
	
	all = latest number of migration

    ./cli migrations/up/3
    ============================ [ Kohana Migrations ] =============================
	    Requested Migration:	3
	    Migrating:		        UP
    --------------------------------------------------------------------------------
    Migrated: 001.sql Initial struct DB
    Migrated: 002.sql Fixtures
    Migrated: 003.sql Table `price` add field `spec`
    ================================================================================
	    Current:		        3
	    Latest:			        7
    ================================================================================
    
## Down

    ./cli migrations/down/0  
	============================ [ Kohana Migrations ] =============================
	    Requested Migration:	0
	    Migrating:		        DOWN
    --------------------------------------------------------------------------------
    Migrated: 003.sql Table `price` add field `spec`
    Migrated: 002.sql Fixtures
    Migrated: 001.sql Initial struct DB
    ================================================================================
	    Current:		        0
	    Latest:			        7
    ================================================================================
    
## Example SQL File

    -- Table `price` add field `spec`

    --UP
    ALTER TABLE `goods_price`
        ADD COLUMN `spec` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `date`;

    -- DOWN
    ALTER TABLE `goods_price` DROP COLUMN `spec`;

# License

	Copyright (c) 2011 Max Kamashev

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.

# Inspiration

Inspiration and base code John Hobbs from https://github.com/jmhobbs/kohana-3-migrations 
and https://code.google.com/p/kohana-migrations/ 
