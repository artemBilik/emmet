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
  (new Emmet('div>p>span+a>img[src=/img.jpg]))->create();
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
