<?php
/**
 * Phanbook : Delightfully simple forum software
 *
 * Licensed under The GNU License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link    http://phanbook.com Phanbook Project
 * @since   1.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 */
namespace Phanbook\Models;

use Phalcon\Validation;
use Phalcon\Validation\Validator\Email as EmailValidator;
use Phalcon\Validation\Validator\Uniqueness as UniquenessValidator;

/**
 * To use for subscribe weekly post
 *
 * @link subscribe__weekly
 * @see  controller subscribe
 */
class Subscribe extends ModelBase
{

    /**
     *
     * @var integer
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $email;

    /**
     *
     * @var string
     */
    protected $status;

    /**
     * @var int
     */
    protected $usersId;


    protected $name;

    /**
     * Method to set the value of field id
     *
     * @param  integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Method to set the value of field email
     *
     * @param  string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Method to set the value of field status
     *
     * @param  string $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Returns the value of field id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value of field email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Returns the value of field status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $usersId
     */
    public function setUsersId($usersId)
    {
        $this->usersId = $usersId;
        return $this;
    }

    /**
     * @return int
     */
    public function getUsersId()
    {
        return $this->usersId;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    public function getName()
    {
        return $this->name;
    }
    /**
     * Validations and business logic
     */
    public function validation()
    {
        $validator = new Validation();
        $validator->add(
            'email',
            new EmailValidator([
                'model' => $this,
                'message' => 'Please enter a correct email address'
            ])
        );
        $validator->add(
            'email',
            new UniquenessValidator([
                'model' => $this,
                'message' => 'Another user with same email already exists'
            ])
        );
        return $this->validate($validator);
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return [
            'id'        => 'id',
            'email'     => 'email',
            'usersId'   => 'usersId',
            'status'    => 'status',
            'name'      => 'name'
        ];
    }
    /**
     * Overwrite messages
     */
    public function getMessages()
    {
        $messages = array();
        foreach (parent::getMessages() as $message) {
            switch ($message->getType()) {
                case 'InvalidCreateAttempt':
                    $messages[] = 'The email already exists';
                    break;
                case 'InvalidUpdateAttempt':
                    $messages[] = 'The email already exists';
                    break;
                case 'PresenceOf':
                    $messages[] = 'The field ' . $message->getField() . ' is mandatory';
                    break;
                case 'Unique':
                    $messages[] = 'The email already exists';
                    break;
                case 'Email':
                    $messages[] = 'The email must have a valid e-mail format';
            }
        }
        return $messages;
    }
}
