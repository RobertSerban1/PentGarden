<?php

class CustomerHandler extends CustomerDAO
{
    public function __construct()
    {
    }
    
    private $executionFeedback;

    public function getExecutionFeedback()
    {
        return $this->executionFeedback;
    }

    public function setExecutionFeedback($executionFeedback)
    {
        $this->executionFeedback = $executionFeedback;
    }

    public function getAllCustomer()
    {
        if ($this->getAll()) {
            return $this->getAll();
        } else {
            return Util::DB_SERVER_ERROR;
        }
    }

    public function getSingleRow($email)
    {
        if ($this->getByEmail($email)) {
            return $this->getByEmail($email);
        } else {
            return Util::DB_SERVER_ERROR;
        }
    }

    public function getCustomerObj($email)
    {
        $c = new Customer();
        $k = $this->getByEmail($email);
        foreach ($k as $v) {
            $c->setId($v->getId());
            $c->setEmail($v->getEmail());
            $c->setPassword($v->getPassword());
            $c->setPhone($v->getPhone());
            $c->setFullName($v->getFullName());
        }
        return $c;
    }

    public function getCustomerObjByCid($id)
    {
        $c = new Customer();
        $k = $this->getByCid($id);
        foreach ($k as $v) {
            $c->setId($v->getId());
            $c->setEmail($v->getEmail());
            $c->setPassword($v->getPassword());
            $c->setPhone($v->getPhone());
            $c->setFullName($v->getFullName());
        }
        return $c;
    }

    public function getUsername($_email)
    {
        $_fullName = null;
        foreach ($this->getSingleRow($_email) as $obj) {
            $_fullName = $obj->getFullName();
        }
        if ($_fullName != null) {
            return $_fullName;
        } else {
            $positionOfAt = strpos($_email, "@");
            return substr($_email, 0, $positionOfAt);
        }
    }

    public function insertCustomer(Customer $customer)
    {
        // insert if value is 0, which means this Customer's email still not registered
        if ($this->isCustomerExists($customer->getEmail()) == 0) {
            if ($this->insert($customer)) {
                $this->setExecutionFeedback("Te-ai inregistrat cu succes! Acum te poti autentifica.");
            } else {
                $this->setExecutionFeedback(Util::DB_SERVER_ERROR);
            }
        } else {
            $this->setExecutionFeedback("Email deja existent.");
        }
    }

    public function updateCustomer(Customer $customer)
    {
        $c_data = [$customer->getId(), $customer->getFullName(), $customer->getPhone(), $customer->getEmail(), $customer->getPassword()];
        $c_data_string = implode(", ", $c_data);
        if(Util::has_reserved_words($c_data_string)) {
            $this->setExecutionFeedback("Ceva nu este corect.");
        } else {
            if ($this->isCustomerExists($customer->getEmail()) == 1) {
                if ($this->update($customer)) {
                    $this->setExecutionFeedback("Ti-ai actualizat profilul cu succes!");
                } else {
                    $this->setExecutionFeedback(Util::DB_SERVER_ERROR);
                }
            } else {
                $this->setExecutionFeedback("Aceasta adresa de email nu este inregistrata.");
            }
        }
    }

    public function deleteCustomer(Customer $customer)
    {
        if ($this->isCustomerExists($customer->getEmail()) == 1) {
            if ($this->delete($customer)) {
                $this->setExecutionFeedback("Ti-ai sters cu succes profilul!");
            } else {
                $this->setExecutionFeedback(Util::DB_SERVER_ERROR);
            }
        } else {
            $this->setExecutionFeedback("Aceasta adresa de email nu este inregistrata.");
        }
    }

    public function isPasswordMatchWithEmail($password, Customer $customer)
    {
        $cust = $this->getSingleRow($customer->getEmail())[0];
        if (password_verify($password, $cust->getPassword())) {
            return 'Parola este invalida!';
        } else {
            return 'Parola invalida!';
        }
    }

    public function totalCustomersCount()
    {
        return count($this->getAllCustomer());
    }

    public function doesCustomerExists($email) {
        return $this->isCustomerExists($email);
    }

    public function handleIsAdmin($email) {
        return $this->isAdminCount($email);
    }
}
