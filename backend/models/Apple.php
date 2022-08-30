<?php

namespace backend\models;


/**
 * This is the model class for table "apple".
 *
 * @property int $id
 * @property int $remain
 * @property int $state
 * @property string $created_at
 * @property string|null $fell_at
 * @property string $color
 */
class Apple extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */

    const STATE_ON_THE_TREE = 1;
    const STATE_ON_THE_GROUND = 2;
    const STATE_ROTTEN = 3;

    const STATES = [
        self::STATE_ON_THE_TREE,
        self::STATE_ON_THE_GROUND,
        self::STATE_ROTTEN,
    ];

    public function getTextState()
    {
        switch($this->state) {
            case self::STATE_ON_THE_TREE: return 'На дереве';
            case self::STATE_ON_THE_GROUND: return 'Упало';
            case self::STATE_ROTTEN: return 'Сгнило';
        }
        return null;
    }


    public static function tableName()
    {
        return 'apple';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['remain', 'state'], 'integer'],
            [['state'], 'in', 'range' => self::STATES],
            [['created_at', 'fell_at'], 'safe'],
            [['color'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'remain' => 'Remain',
            'state' => 'State',
            'created_at' => 'Created At',
            'fell_at' => 'Fell At',
            'color' => 'Color',
        ];
    }

    public function beforeSave($insert) : bool
    {
        if($insert) {
            $this->created_at = (new \DateTime())->format("Y-m-d H:i:s");
            $this->remain = 100;
            $this->state = self::STATE_ON_THE_TREE;
        }
        return parent::beforeSave($insert);
    }

    public function fallToGround() : bool
    {
        if($this->state == self::STATE_ON_THE_TREE) {
            $this->state = self::STATE_ON_THE_GROUND;
            $this->fell_at = (new \DateTime())->format("Y-m-d H:i:s");
            return $this->save();
        }
        return false;
    }

    public function checkIfRotten() : bool
    {
        if($this->state == self::STATE_ON_THE_GROUND and (new \DateTime($this->fell_at))->getTimestamp() + 5 * 60 * 60 < (new \DateTime())->getTimestamp()) {
            $this->state = self::STATE_ROTTEN;
            $this->save();
            return true;
        }
        return false;
    }

    public function eat(int $percent) : int
    {
        if($this->state == self::STATE_ON_THE_GROUND) {
            if($this->checkIfRotten()) {
                throw new AppleException("Нельзя съесть гнилое яблоко");
            }
            $this->remain -= $percent;
            if($this->remain <= 0) {
                $this->delete();
                return 0;
            }
            $this->save();
            return $this->remain;
        } elseif($this->state == self::STATE_ROTTEN) {
            throw new AppleException("Нельзя съесть гнилое яблоко");
        }

        throw new AppleException("Нельзя съесть яблоко на дереве");
    }
}

class AppleException extends \Exception {
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
