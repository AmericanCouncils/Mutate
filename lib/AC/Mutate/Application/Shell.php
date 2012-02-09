<?php

namespace AC\Mutate\Application;
use \Symfony\Component\Console\Shell as BaseShell;

class Shell extends BaseShell {

    protected function getHeader()
    {
        return <<<EOF
<info>

    o          o                 o                    o                  
   <|\        /|>               <|>                  <|>                 
   / \\o    o// \               < >                  < >                 
   \o/ v\  /v \o/   o       o    |         o__ __o/   |        o__  __o  
    |   <\/>   |   <|>     <|>   o__/_    /v     |    o__/_   /v      |> 
   / \        / \  < >     < >   |       />     / \   |      />      //  
   \o/        \o/   |       |    |       \      \o/   |      \o    o/    
    |          |    o       o    o        o      |    o       v\  /v __o 
   / \        / \   <\__ __/>    <\__     <\__  / \   <\__     <\/> __/> 

</info>
EOF
		.parent::getHeader();
    }
	
}
