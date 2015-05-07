# emmet
Emmet for php


Global Usage


<code><?php 

  (new Emmet('div>p>span+a>img[src=/img.jpg]))->create();
  
  // OR
   
  $emmet = new Emmet('tr>td{`value`}');
  
  foreach($data as $value){
      echo $emmet->create([ 'value' => $value ]);
  }
  
  //  Don't use Emmet in way like
  
  foreach($data as $value){
      echo (new Emmet('tr>td{`value`}))->create([ 'value' => $value ]);
  }
?></code>
