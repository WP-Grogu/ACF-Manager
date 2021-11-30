# ACF Manager

This librairy helps you manage your ACF Fields Groups, layouts, blocks (gutemberg). 
It uses a fork of [extended-acf](https://github.com/wordplate/extended-acf) on the background.

## Getting started

To get started, grab the library thru composer in your theme directory : 

```
composer require wp-grogu/acf-manager
```

Then, create an `Acf` folder inside your `app/` folder. You should use a PSR autoload logic.  

## Create your groups

Groups are represented by classes. Each Field Groups has fields and location(s).  
Fields and locations are using the great `wordplate/extended-acf` package, coming with a explicit documentation, so make sure to checkout [the official repositiory](https://github.com/wordplate/extended-acf).

### Simple field group definition
### Flexible field group definition
### Gutemberg block field group definition

## Register the groups into ACF/Wordpress

### Using ObjectPress
### Manually
## Retreive your groups

### The Fieldset class
### Casting returned values using Transformers

#### Using transformers
#### Creating transformers