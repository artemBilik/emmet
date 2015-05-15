# emmet
Emmet for php
# Interface
````

Emmet::__construct( string emmet_string )
Emmet::create(array data)

````

# Global Usage


Simple Usage
``````````
  (new Emmet('div>p>span+a>img[src=/img.jpg]'))->create();
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
    ['data' => $data,'tr_cnt' => count($data),]
);

```

# Detail usage

[operation] [ tag[id][class][attributes][element text node][multiplication] ] | [ html[multiplication] ] | [ text_node[multiplication] ] [operation]

So we have Operations and Tags, Text Nodes and Html elements.

# Operations
 
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
'(div>p+a)+div' === '<div><p></p><a></a></div>' . '<div></div'
'(div>p>a>span)>p === '<div>' . <p><a><span></span></a></p>' . '<p></p>' . '</div>'
'div>(div>p)^+div === '<div><div><p></div>' . '<div></div>'
```

# Tags

To create a tag you can use any character.
```
`div+h1` === '<div></div><h1></h1>'
```
You can add an id to your tag with "#"
```
'div#myDiv' = '<div id="myDiv></div>'
```
You can add a class with "."
Use " " to add more than one class
```
'div.class1+civ.class1 class2' === '<div class="class1"></div><div class="class1 class2"></div>'
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

# Text Node

You can create a text node without any tag.
And use it like other element with "+" operation. But you cann't add a child element to text node.
```
'p+{ some text }+a' === '<p></p> some text <a></a>'
'p+{ some text }*2' === '<p></p some text  some text'
```

# Variables

You can use a variables like a value of your id, classes, text nodes, or multiplication in your string with " ` ".

```
(new Emmet('p.`info_class`{`information`}+span'))->create([ 'information' => 'some information for user', 'info_class' => 'info']) 
 === '<p class="info">some information for user</p><span></span>'
 ```
 You have a special variable "$". which represent a number of your element. the number of element is 0.
 But if you use a multiplication for your element it will change.
 ```
 echo (new Emmet('ul>li{`ul[$]`}*2))->create(['ul' => [1,2,3]]) === '<ul><li>1</li><li>2</li></ul>'
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

echo (new Emmet())

```

