<?php

/**
 * This is the model class for table "{{partner}}".
 *
 * The followings are the available columns in table '{{partner}}':
 * @property string $id
 * @property string $root
 * @property string $lft
 * @property string $rgt
 * @property integer $level
 * @property string $name
 * @property string $description
 */
class Partner extends CActiveRecord
{
    const ROLE_ADMIN = 1;//роль - пользователь
    const ROLE_USER = 0;//роль - админ системы

    //уровни в партнёрской программе
    const SILVER_LEVEL = 1;
    const GOLD_LEVEL = 2;
    const PLATINUM_LEVEL = 3;
    const DIAMONT_LEVEL = 4;

    //статус партнёра
    const STATUS_MEMBER = 0;//участник
    const STATUS_Partner = 1;//партнер

    const SALT = '159753159753';

    // ссылка на ajax-запрос для получения таблицы данными
    public $ajaxlink;
    public $type;


    public function treechild($id, $recursive = true)
    {
        $curnode = Partner::model()->findByPk($id);
        if($curnode){
            $childrens = $curnode->children()->findAll();
            if(sizeOf($childrens)>0){
                $out = array();
                foreach($childrens as $children){
                    if($recursive){
                        //$currow=['id'=>$children->id,'text'=>$children->fio,'children'=>$this->treechild($children->id)];
                        $currow=array('id'=>$children->id,'text'=>$children->fio,'children'=>$this->treechild($children->id));
                    }else{
                        $curnode = Partner::model()->findByPk($children->id);
                        $referals = $curnode->children()->findAll();
                        if(sizeOf($referals)>0){
                            $hasChildren = true;
                        }else{
                            $hasChildren = false;
                        }
                        //$currow=['id'=>$children->id,'text'=>$children->fio, 'hasChildren'=>$hasChildren];
                        $currow=array('id'=>$children->id,'text'=>$children->fio, 'hasChildren'=>$hasChildren);
                    }

                    $out[]=$currow;
                }
                return $out;
            }
            else{
                return null;
            }
        }
        return null;
    }

    public function treedata($root, $showRoot = true)
    {
        if($showRoot){
            $root = Partner::model()->findByPk($root);
            $out = array();
            // получаем список дочерних элементов, без рекурсии
            //$currow=['id'=>$root->id,'text'=>$root->fio,'children'=>$this->treechild($root->id, false)];
            $currow=array('id'=>$root->id,'text'=>$root->fio,'children'=>$this->treechild($root->id, false));
            $out[]=$currow;

            return $out;
        }else{
            $roots=Partner::model()->findByPk($root);
            $ancestors=$roots->children()->findAll();

            $out = array();
            foreach($ancestors as $ancestor){
                //$currow=['id'=>$ancestor->id,'text'=>$ancestor->fio,'children'=>$this->treechild($ancestor->id, false)];
                $currow=array('id'=>$ancestor->id,'text'=>$ancestor->fio,'children'=>$this->treechild($ancestor->id, false));
                $out[]=$currow;
            }
            return $out;
        }
    }

    public function behaviors()
    {
        return array(
            'nestedSetBehavior'=>array(
                'class'=>'application.behaviors.NestedSetBehavior',
                'leftAttribute'=>'lft',
                'rightAttribute'=>'rgt',
                'levelAttribute'=>'level',
                'hasManyRoots'=>false,
            ),
            'PartnerProgrammBehavior'=>array(
                'class'=>'application.behaviors.PartnerProgrammBehavior'
            )
//            'TreeBehavior' => array(
//              'class' => 'application.behaviors.XTreeBehavior',
//                'treeLabelMethod'=> 'getTreeLabel',
//                'menuUrlMethod'=> 'getMenuUrl',
//            ),
        );
    }

    public function getStatusPartner(){

        if($this->status==self::STATUS_MEMBER){
            return 'Участник';
        }

        if($this->status==self::STATUS_Partner){
            return 'Партнер';
        }

        return $this->status;
    }

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Partner the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{partner}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			//array('lft, rgt, level, name, description', 'required'),
			array('level', 'numerical', 'integerOnly'=>true),
			//array('root', 'length', 'max'=>20),
			//array('lft, rgt', 'length', 'max'=>10),
			//array('fio', 'length', 'max'=>128),
            array('fio, type', 'length', 'max'=>256),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id,  lft, rgt, level, type, status', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
        return array(

            //'parent' => array(self::BELONGS_TO, 'Partner', 'root'),

            // кол-во НЕ_именных партнёрских комплектов
            'partnershipCount' => array(self::STAT, 'BuyingPartnershipSet', 'who_buys',
                'condition'=>'type_buying='.BuyingPartnershipSet::TYPE_NONAME
            ),
//
//            'children' => array(self::HAS_MANY, 'Partner', 'parent_id', 'order' => 'level'),
//
//            //всего дочерних сотрудников
//            'childCount' => array(self::STAT, 'Partner', 'parent_id'),
//
//            //сотрудников дочерних в статусе - Партнёр
//            'childCountPartner' => array(self::STAT, 'Partner', 'parent_id', 'condition'=>'status='.self::STATUS_Partner),
//
//            //сотрудников дочерних в статусе - Партнёр, уровня(1)
//            'childCountPartnerLevel1' => array(self::STAT, 'Partner', 'parent_id',
//                'condition'=>'status='.self::STATUS_Partner.' AND parent_id='.Yii::app()->user->id.''
//            ),
//
//            //сотрудников дочерних в статусе - Участник
//            'childCountMember' => array(self::STAT, 'Partner', 'parent_id', 'condition'=>'status='.self::STATUS_MEMBER),

        );
    }

    /*
     * формируем аякс ссылку для просмотра детальной иинформации по статусу либо уровню нужных юзеров
     */
    public function getAjaxlink($title='Подробнее', $url_action='', $post_data=''){
        return
            CHtml::ajaxLink(
                $title,
                $url_action,
                array(
                    'type' => 'GET',
                    // можно спросить до отправки что-то или проверить данные какие-нибудь.
                    'beforeSend' => "function(request){
                    }",
                    'success' => "function(data){
                        $('#tbl').show();
                        $('#counters').hide();
                        $('#show_counters').show();
                        $('#tbl').html(data);// получили данные - обновили DIV
                    }",
                    'data' => $post_data, // посылаем значения
                    'cache'=>'false' // если нужно можно закешировать
                ),
                array( // самое интересное
                    'href' => 'javascript: void(0)',// подменяет ссылку на левую
                    //'class' => "sadfsadfsadclass" // добавляем какой-нить класс для оформления
                    'id'=>uniqid(),
                    'style'=>'margin-left:40px;'
                )
            );
    }

    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'root' => 'Root',
			'lft' => 'Lft',
			'rgt' => 'Rgt',
			'level' => 'Level',
			'name' => 'Name',
            'fio' => 'ФИО',
			'phone' => 'Телефон',
            'status'=>'Статус',
            'email'=>'Почта',
            //'status'=>'Статус',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

//		$criteria->compare('id',$this->id,true);
//		//$criteria->compare('root',$this->root,true);
//		$criteria->compare('lft',$this->lft,true);
//		$criteria->compare('rgt',$this->rgt,true);
//		$criteria->compare('level',$this->level);
//		$criteria->compare('name',$this->name,true);
//		$criteria->compare('description',$this->description,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /*
     * в зависимости от статуса пользователя - возвращаем текстовое представление уровня пользователя в партнёрской программе
     */
    public function getPartnerLevel(){

        //если статус - участник, то нет уровня
        if($this->status==self::STATUS_MEMBER || $this->partner_level==0){
            return 'Нет уровня';
        }

        //2 - серебряный
        if($this->partner_level==self::SILVER_LEVEL){
            return 'Серебряный';
        }

        //3 - золотой
        if($this->partner_level==self::GOLD_LEVEL){
            return 'Золотой';
        }

        //4 - платиновый
        if($this->partner_level==self::PLATINUM_LEVEL){
            return 'Платиновый';
        }

        //5- бриллиантовый
        if($this->partner_level==self::DIAMONT_LEVEL){
            return 'Бриллиантовый';
        }
        return $this->partner_level;
    }

    /**
     * Suggests a list of existing values matching the specified keyword.
     * @param string the keyword to be matched
     * @param integer maximum number of names to be returned
     * @return array list of matching lastnames
     */
    public function suggest($keyword,$limit=20)
    {
        $models=$this->findAll(array(
            'condition'=>'label ILIKE :keyword',
            'limit'=>$limit,
            'params'=>array(':keyword'=>"$keyword%")
        ));
        $suggest=array();
        foreach($models as $model) {
            $suggest[] = array(
                'label'=>$model->TreeBehavior->pathText,  // label for dropdown list
                'value'=>$model->label,  // value for input field
                'id'=>$model->id,       // return values from autocomplete
            );
        }
        return $suggest;
    }

    /*
     * проверка типа ajax-запроса, для выборки дочерних элементов по фильтру
     */
    static function validateTypeAjax($type){

        if($type=='all'){ return true; }

        if($type=='partner_level1'){ return true; }

        if($type==self::STATUS_MEMBER){ return true; }

        if($type==self::STATUS_Partner){ return true; }

        return false;
    }

    /*
     * формирование хеша пароля, с определённой СОЛЬЮ
     */
    public function encrypting($password){
        return md5($password.md5(self::SALT));
    }
}