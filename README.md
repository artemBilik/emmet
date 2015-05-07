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
      echo (new Emmet('tr>td{`value`}))->create([ 'value' => $value ]);
  }
```
   Because it's work like prepared Statement in PDO.<br />
   <strong>Emmet::__construct()</strong> will prepare the html tree.<br />
   And <strong>Emmet::create()</strong> will use this tree.<br />

Best practice to create a table html element is
```
echo (new Emmet(
      'table#myTable>tbody>tr.myTr*`tr_cnt`>td.myTd{`data[@][$]`*`td_cnt`}'
  ))->create([ 
      'data' => $data, 
      'tr_cnt' => count($data), 
      'td_cnt' => count($data[0]
  )]);
```
