<?php
namespace vsm\bl;

class Generator {


    private $classDefinition;
    private $class;
    private $properties;

    public function setClass($class){
        $this->class = $class;
        $this->properties=[];
        $this->Initialize();
        return $this;
    }

    public function __construct($class)
    {
        $this->setClass($class);
    }


    public function getProperties(){
        return $this->properties;
    }

    public function getDefinitions($key=null){
        if($key!=null && isset($this->classDefinition->$key)) return $this->classDefinition->$key;
        return $this->classDefinition;
    }

    protected function Initialize(){

        $classReflection = new \ReflectionClass($this->class);
        $properties = $classReflection->getProperties();
        $mixed = [];
        foreach ($properties as $property){
            if($property->name=='table') continue;
            $this->properties[]=$property->name;
            $mixed[$property->name] = $this->GetDocParams($property->name);
        }
        $this->classDefinition = (object)$mixed;

    }

    public function GetDocParams($property){
        $ref = new \ReflectionProperty($this->class,$property);
        $docCommect = $ref->getDocComment();

        $params = [
            'type'=>'string',
            'required'=>false,
            'PdoType'=>\PDO::PARAM_STR,
            'pk'=>false
        ];
        if(!$docCommect) return $params;

        preg_match_all("#.*\@var\s([a-z]+)#",$docCommect,$matches);
        if(isset($matches[1][0])){
            $params['type']= $matches[1][0];
        }

        $result = preg_match("#.*\@required.*#",$docCommect);
        if($result){
            $params['required']= true;
        }

        $result = preg_match("#.*\@primaryKey.*#",$docCommect);
        if($result){
            $params['pk']= true;
        }


        $type = \PDO::PARAM_STR;
        switch ($params['type']){
            case 'integer':
                $type = \PDO::PARAM_INT;
                break;
            case 'string' || 'date':
                $type = \PDO::PARAM_STR;
                break;
            case 'bool':
                $type = \PDO::PARAM_BOOL;
                break;
            default:
                break;

        }

        $params['PdoType']=$type;

        return (object)$params;
    }
}