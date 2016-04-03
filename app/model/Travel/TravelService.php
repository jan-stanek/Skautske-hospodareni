<?php

namespace Model;

/**
 * @author Hána František <sinacek@gmail.com>
 * správa cestovních příkazů
 */
class TravelService extends BaseService {

    protected $tableTravel;
    protected $tableContract;
    protected $tableVehicle;

    public function __construct(\DibiConnection $connection) {
        parent::__construct(NULL, $connection);
        /** @var TravelTable */
        $this->table = new CommandTable($connection);
        $this->tableTravel = new TravelTable($connection);
        $this->tableContract = new ContractTable($connection);
        $this->tableVehicle = new VehicleTable($connection);
    }

    public function isContractAccessible($contractId, $unit) {
        if (($contract = $this->getContract($contractId))) {
            return $contract->unit_id == $unit->ID ? TRUE : FALSE;
        }
        return FALSE;
    }

    public function isCommandAccessible($commandId, $unit) {
        if (($command = $this->getCommand($commandId))) {
            return $command->unit_id == $unit->ID ? TRUE : FALSE;
        }
        return FALSE;
    }

    public function isVehicleAccessible($vehicleId, $unit) {
        return $this->getVehicle($vehicleId, true)->unit_id == $unit->ID ? TRUE : FALSE;
    }

    /**     VEHICLES    */

    /**
     * vraci detail daného vozidla
     * @param type $vehicleId - ID vozidla
     * @param type $withDeleted - i smazana vozidla?
     * @return type
     */
    public function getVehicle($vehicleId, $withDeleted = false) {
        $cacheId = __FUNCTION__ . "_" . $vehicleId . "_" . (int) $withDeleted;
        if (!($res = $this->loadSes($cacheId))) {
            $res = $this->tableVehicle->get($vehicleId, $withDeleted);
            $this->saveSes($cacheId, $res);
        }
        return $res;
    }

    public function getVehiclesPairs($unitId) {
        return $this->tableVehicle->getPairs($unitId);
    }

    public function getAllVehicles($unitId) {
        $all = $this->tableVehicle->getAll($unitId);
        //TODO predelat na databazi
        foreach ($all as $key => $value) {
            $command = $this->getAllCommandsByVehicle($unitId, $value->id);
            $all[$key]['commands'] = count($command);
        }
        return $all;
    }

    public function addVehicle($data) {
        return $this->tableVehicle->add($data);
    }

    public function removeVehicle($vehicleId, $unitId) {
        $commands = $this->getAllCommandsByVehicle($unitId, $vehicleId);
        if (count($commands) > 0) { //nelze mazat vozidlo s navazanými příkazy
            return false;
        }
        return $this->tableVehicle->remove($vehicleId);
    }

    /**     TRAVELS    */
    public function getTravel($commandId) {
        return $this->tableTravel->get($commandId);
    }

    public function getTravels($commandId) {
        return $this->tableTravel->getAll($commandId);
    }

    public function addTravel($data) {
        return $this->tableTravel->add($data);
    }

    public function updateTravel($data, $tId) {
        return $this->tableTravel->update($data, $tId);
    }

    public function deleteTravel($travelId) {
        return $this->tableTravel->delete($travelId);
    }

    public function getTravelTypes($pairs = FALSE) {
        return $this->tableTravel->getTypes($pairs);
    }

    /**     CONTRACTS    */
    public function getContract($contractId) {
        $cacheId = __FUNCTION__ . "_" . $contractId;
        if (!($res = $this->loadSes($cacheId))) {
            $res = $this->tableContract->get($contractId);
            $this->saveSes($cacheId, $res);
        }
        return $res;
    }

    public function getAllContracts($unitId) {
        return $this->tableContract->getAll($unitId);
    }

    public function getAllContractsPairs($unitId) {
        $data = $this->getAllContracts($unitId);
        $res = array("valid" => array(), "past" => array());

        foreach ($data as $i) {
            if(is_null($i->end)) {
                $res["valid"][$i->id] = $i->driver_name;
            } else {
                if ($i->end->format("U") > time()) {
                    $res["valid"][$i->id] = $i->unit_person . " <=> " . $i->driver_name . " (platná do " . $i->end->format("j.n.Y") . ")";
                } else {
                    if ($i->end->format("Y") < date("Y") - 1) {#skoncila uz predloni
                        continue;
                    }
                    $res["past"][$i->id] = $i->unit_person . " <=> " . $i->driver_name . " (platná do " . $i->end->format("j.n.Y") . ")";
                }
            }
        }
        return $res;
    }

    public function addContract($values) {
        if (!$values['end'] && !is_null($values["start"])) {
            $values['end'] = date("Y-m-d", strtotime("+ 3 years", $values["start"]->getTimestamp()));
        } //nastavuje platnost smlouvy na 3 roky
        return $this->tableContract->add($values);
    }

    public function deleteContract($contractId) {
        return $this->tableContract->delete($contractId);
    }

    /**     COMMANDS    */
    public function getCommand($commandId) {
        $cacheId = __FUNCTION__ . "_" . $commandId;
        if (!($res = $this->loadSes($cacheId))) {
            $res = $this->table->get($commandId);
            $this->saveSes($cacheId, $res);
        }
        return $res;
    }

    public function addCommand($cmd) {
        $types = $cmd["type"];
        unset($cmd["type"]);
        $cmd->fuel_price = (float)$cmd->fuel_price;
        $cmd->amortization = (float)$cmd->amortization;
        $insertedId = $this->table->add($cmd);
        $this->table->updateTypes($insertedId, $types);
        return $insertedId;
    }

    public function updateCommand($v, $unit, $id) {
        if (!$this->isContractAccessible($v['contract_id'], $unit)) {
            return FALSE; //neoprávěný přístup
        }
        $types = $v["type"];
        unset($v["type"]);
        $status = $this->table->update($v, $id);
        return $status || $this->table->updateTypes($id, $types);
    }

    public function getAllCommands($unitId) {
        return $this->table->getAll($unitId);
    }

    /**
     * vraci všechny přikazy navazane na smlouvu
     * @param type $unitId
     * @param type $contractId
     * @return type 
     */
    public function getAllCommandsByContract($unitId, $contractId) {
        return $this->table->getAllByContract($unitId, $contractId);
    }

    /**
     * vraci všechny přikazy navazane na vozidlo
     * @param type $unitId
     * @param type $vehicleId
     * @return type 
     */
    public function getAllCommandsByVehicle($unitId, $vehicleId) {
        return $this->table->getAllByVehicle($unitId, $vehicleId);
    }

    /**
     * uzavře cestovní příkaz a nastavi cas uzavření
     * @param type $commandId
     */
    public function closeCommand($commandId) {
        return $this->table->changeState($commandId, date("Y-m-d H:i:s"));
    }

    public function openCommand($commandId) {
        return $this->table->changeState($commandId, NULL);
    }

    public function deleteCommand($commandId) {
        return $this->table->delete($commandId);
    }

    public function getCommandTypes($commandId) {
        return $this->table->getCommandTypes($commandId);
    }

}
