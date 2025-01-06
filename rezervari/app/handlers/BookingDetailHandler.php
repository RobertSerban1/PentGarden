<?php

class BookingDetailHandler extends BookingDetailDAO
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

    public function getAllBookings()
    {
        if ($this->fetchBooking()) {
            return $this->fetchBooking();
        } else {
            return Util::DB_SERVER_ERROR;
        }
    }

    public function getCustomerBookings(Customer $c)
    {
        if ($this->fetchBookingByCid($c->getId())) {
            $this->setExecutionFeedback(1);
            return $this->fetchBookingByCid($c->getId());
        }
        return $this->setExecutionFeedback(0);
    }

    public function getPending()
    {
        $count = 0;
        $pending = \models\StatusEnum::PENDING_STR;
        foreach ($this->getAllBookings() as $v) {
            if (($v["status"] == $pending) || (strtoupper($v["status"]) == $pending)) {
                $count++;
            }
        }
        return $count;
    }

    public function getConfirmed()
    {
        $count = 0;
        $confirmed = \models\StatusEnum::CONFIRMED_STR;
        foreach ($this->getAllBookings() as $v) {
            if (($v["status"] == $confirmed) || (strtoupper($v["status"]) == $confirmed)) {
                $count++;
            }
        }
        return $count;
    }

    public function confirmSelection($item)
    {
        for ($i = 0; $i < count($item); $i++) {
            if (is_numeric($item[$i])) {
                if ($this->updateConfirmed($item[$i])) {
                    $out = " Aceste rezervari au fost cu succes <b>confirmate</b>.";
                    $out .= " Pagina se va reincarca pentru a reflecta modificarile.";
                    $this->setExecutionFeedback($out);
                } else {
                    $this->setExecutionFeedback("Trebuie sa existe o eroare la procesarea cererii dvs. Va rugam sa incercati din nou mai tarziu.");
                }
            }  else {
                $this->setExecutionFeedback("Ceva nu este în regula!");
            }
        }
    }

    public function cancelSelection($item)
    {
        for ($i = 0; $i < count($item); $i++) {
            /*
            if ($this->updateBooking($item[$i], false, true)) {
                $out = " Aceste rezervari au fost cu succes <b>anulate</b>.";
                $out .= " Pagina se va reincarca pentru a reflecta modificarile.";
                $this->setExecutionFeedback($out);
            } else {
                $this->setExecutionFeedback("Trebuie sa existe o eroare la procesarea cererii dvs. Va rugam sa incercati din nou mai tarziu.");
            }
            */
            if (is_numeric($item[$i])) {
                if ($this->updateCancelled($item[$i])) {
                    $out = "Aceste rezervari au fost cu succes <b>anulate</b>.";
                    $out .= " Pagina se va reincarca pentru a reflecta modificarile.";
                    $this->setExecutionFeedback($out);
                } else {
                    $this->setExecutionFeedback("Trebuie sa existe o eroare la procesarea cererii dvs. Va rugam sa incercati din nou mai tarziu.");
                }
            } else {
                $this->setExecutionFeedback("Ceva nu este in regula!");
            }
        }
    }
}

// todo: protect booking functionalities (only admin can perform)