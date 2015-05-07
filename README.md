# emmet
Emmet for php


# Global Usage


``````````
  use \emmet\Emmet;
  require_once PATH_TO_EMMET . '/Emmet.php';
  
  (new Emmet('div>p>span+a>img[src=/img.jpg]))->create();
  
  // OR
   
  $emmet = new Emmet('tr>td{\`value\`}');
  
  foreach($data as $value){
      echo $emmet->create([ 'value' => $value ]);
  }
  
  //  Don't use Emmet in way like
  
  foreach($data as $value){
      echo (new Emmet('tr>td{`value`}))->create([ 'value' => $value ]);
  }
  
  // Because it's work like prepared Statement in PDO
  // Emmet::__construct( string emmet_string ) will prepare the html tree 
  // And Emmet::create(array data) will use this tree
  
````````````
