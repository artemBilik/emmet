# Description
Emmet implementation for php
# Installation
Add 
```
"artem_c/emmet": "~1.0"
```
to the require section of your composer.json file.

# Interface
````

Emmet::__construct( string emmet_string );
Emmet::create(array data);
Emmet::addFunctions(array functions);

````

# Global Usage


Simple Usage
``````````
  (new Emmet('div>p>span+a>img[src=img.jpg]'))->create();
```
   OR

```
  $emmet = new Emmet('tr>td{`value`}');
  
  foreach($data as $value){
      echo $emmet->create([ 'value' => $value ]);
  }
```

 Don't use Emmet in way like
  
```
  foreach($data as $value){
      echo (new Emmet('tr>td{`value`}'))->create([ 'value' => $value ]);
  }
```
   Because it's work like prepared Statement in PDO.<br />
   <strong>Emmet::__construct()</strong> will prepare the html tree.<br />
   And <strong>Emmet::create()</strong> will use this tree.<br />

Best practice to create a table html element is
```
echo (new Emmet(
    'table#myTable>tbody>tr.myTr*`tr_cnt`>td.title{`data[$][title]`}+td{`data[$][value]`}')
)->create(
    ['data' => [['title' => 't1', 'value' => 'v1'], ['title' => 't2', 'value' => 'v2'], ['title' => 't3', 'value' => 'v3']], 'tr_cnt' => 3]
);

```

# Detail usage

[operation] [ tag [id] [class] [attributes] [element text node] [multiplication] ] | [ html [multiplication] ] | [ text_node [multiplication] ] [operation]

So we have Operations and Tags, Text Nodes and Html elements.

## Overview
Emmet string consists of objects and operations. Objects represent by tag or text node or html.
```
object+object>object(object+object)
```
Tag object starts from a tag name
```
div>div>p+span
```
It can start from any charaсter except '`', '%', '{'.
Tag node can has id, class, attributes, text and multiplication.
```
div#id.class[attr=value]{text}*2+span.class
```
Text node object starts from '{'. And can has multiplication
```
{text}+{another text}*3
```
Text node cann't has any child. So you cann't use '>' operation to the text node object. 

Html object represent by variable or function. It can has a multiplication.
```
`variable`>%function()%
```
It can has a child object.

## Operations
 
( ) ^ > +
 
Use "+" operation to add sibling to previous elements

```
'a+span'  ==== '<a></a><span></span>'
```

Use ">" operation to add child to previous element

```
'a>span' === '<a><span></span></a>'
```

Use "^" operation to climb up on the tree
```
'p>a>img^+span' === '<p><a><img /></a><span></span></p>'
```
Use "( )" operations for groupping elements
Should to know that next after ")" operation will use the first element in the brackets.
Let's see.
```
'(div>p+a)+div' === '<div><p></p><a></a></div>' . '<div></div>'
'(div>p>a>span)>p' === '<div>' . <p><a><span></span></a></p>' . '<p></p>' . '</div>'
'div>(div>p)^+div' === '<div><div><p></p></div></div>' . '<div></div>'
```

## Tags

To create a tag you can use any character.
```
`div+h1` === '<div></div><h1></h1>'
```
You can add an id to your tag with "#"
```
'div#myDiv>span' = '<div id="myDiv"><span></span></div>'
```
You can add a class with "."
Use " " to add more than one class
```
'div.class1+div.class1 class2' === '<div class="class1"></div><div class="class1 class2"></div>'
```
To add any other attribute use "[ ]" 
```
'option[value=12 selected]' === '<option value="12" selected="selected"></option>'
```
To add a text inside your tag use "{ }"
```
'p{some text}' === '<p>some text</p>'
```
If you need more than one elements use multiplication by "*"
```
'p*2' === '<p></p><p></p>'
```

## Text Node

You can create a text node without any tag.
And use it like other element with "+" operation. But you cann't add a child element to text node.
```
'p+{ some text }+a' === '<p></p> some text <a></a>'
'p+{ some text }*2' === '<p></p> some text  some text '
```

## Variables

You can use a variables like a value of your id, classes, text nodes, or multiplication in your string with " ` ".

```
(new Emmet('p.`info_class`{`information`}+span'))->create([ 'information' => 'some information for user', 'info_class' => 'info']) 
 === '<p class="info">some information for user</p><span></span>'
 ```
 You have a special variable "$". which represent a number of your element. the number of element is 0.
 But if you use a multiplication for your element it will change.
 ```
 echo (new Emmet('ul>li{`ul[$]`}*2'))->create(['ul' => [1,2,3]]) === '<ul><li>1</li><li>2</li></ul>'
 ```
 Or if parent element has an multiplication than the child will have the same multiplication
 ```
echo (new Emmet(
    'table#myTable>tbody>tr.myTr*`tr_cnt`>td.title{`data[$][title]`}+td{`data[$][value]`}')
)->create(
    ['data' => $data,'tr_cnt' => count($data),]
);
```

You can use an object in your variable by '.'
```

echo (new Emmet('article{`object.title`}'))->create(['object' => new Object(])]);

```

## Functions

You can use a function in your emmet string.
At first you must add a function.
```
Emmet::addFunctions(['funcName' => function() { return 'funcName';}])
```

After this you can call it inside your emmet string by using " % ".

```
echo (new Emmet('p{%funcName()%}'))->create() === '<p>funcName</p>'
```
You can pass an arguments in your function
```
Emmet::addFUnctions(['funcName' => function($arg) { return ' ' . $arg . ' '; }])
```
 Pass the text as argument
```
echo (new Emmet('p{%funcName(some text)%}'))->create() === '<p> some text </p>'
```
 Pass the variable as argument
 ```
 echo (new Emmet('p{%funcName(`arg`)%}'))->create(['arg' => 'arg value']) === '<p> arg value </p>'
 ```
 And you can pass more than one argument
 ```
 Emmet::addFunctions(['func' => function($a, $b, $c) { return $a.$b.$c; }]);
 echo (new Emmet('p{%func(`a`, b, `c`)%}'))->create(['a' => 'aaa', 'c' => 'ccc']) === '<p>aaabccc</p>'
 ```
 
 Your function can be a string
 ```
 Emmet::addFunctions(['infoHeader' => 'Information header'])
 echo (new Emmet('div>header{%infoHeader()%}+section{some info}'))-create() === '<div><header>Information header</header><section>some info</section></div>'
 ```
 
 ## Combine value
 
 You can combine value of your tag or id or class etc...
 With strings variables and functions.
 
 ```
 echo (new Emmet('p#identifier_`$`{the value of node is %getValue(`value[$]`)%, the number of node is `$`}*%count(`value`)%'))->create(['value' => [0,10,20,30,40,50]]) === '<p id="identifier_0">the value of node is 0, the number of node is 0 </p>...<p id="identifier_5">the value of node is 50, the number of node is 5</p>'
 ```
 
## HTML Node

HTML it is a Node of your html tree, and the value of this node is variable or function.
You can add the value of the html node inside tag or another html node or sibling it.


```
Emmet::addFunctions(['htmlFunction' => function(){ return 'function html node';}])
echo (new Emmet('div>`htmlVar`+%htmlFunction()%'))->create(['htmlVar' => 'variable html node']) === '<div>variable html nodefunction html node</div>'
```

Or you can add another nodes to html node.
If you use a variable or your function is a string use '{{value}}' in your string.
If you use a callable function use the last arg in your function

```

echo (new Emmet('div+`myP`>a+span'))->create(['myP' => '<p class="myP">{{value}}</p>']) === '<div></div><p class="myP"><a></a><span></span></p>'

Emmet::addFunction(['oneMoreP' => '<p class="one more p">{{value}}</p>']);

echo (new Emmet('div>%oneMoreP()%>`myP`>a+a'))->create(['myP' => '<p class="myP">{{value}}</p>']) === '<div><p class="one more p"><p class="myP"><a></a><a></a></p></p></div>'

Emmet::addFunction(['func' => function($first, $second, $value) { return $first.' '.$second.' '.$value; }]);

echo (new Emmet('div>%func(first, `second`)%>`second`+a'))->create(['second' => 'second']) === 
'<div>first second second<a></a></div>'

```

