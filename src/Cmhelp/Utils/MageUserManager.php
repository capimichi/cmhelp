<?php

namespace Cmhelp\Utils;

class MageUserManager
{

    /**
     * MagerUserManager constructor.
     * @param $mageDir
     */
    public function __construct($mageDir)
    {
        $mage = rtrim($mageDir, "/") . "/app/Mage.php";
        include_once $mage;
        \Mage::app('admin');
    }

    /**
     * @return array
     */
    public function getUsernames()
    {
        $adminUser = \Mage::getModel('admin/user');
        $collection = $adminUser->getCollection()->load();
        $users = $collection->getData();
        $usernames = [];
        foreach ($users as $user) {
            $usernames[] = $user['username'];
        }
        return $usernames;
    }

    /**
     * @param $username
     * @param $name
     * @param $lastname
     * @param $email
     * @param $password
     */
    public function addAdmin($username, $name, $lastname, $email, $password)
    {
        try {
            $adminUserModel = \Mage::getModel('admin/user');
            $adminUserModel->setUsername($username)
                ->setFirstname($name)
                ->setLastname($lastname)
                ->setEmail($email)
                ->setNewPassword($password)
                ->setPasswordConfirmation($password)
                ->setIsActive(true)
                ->save();
            $adminUserModel->setRoleIds(array(1))
                ->setRoleUserId($adminUserModel->getUserId())
                ->saveRelations();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}