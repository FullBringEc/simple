<?php

class ObnDatosSeguimientoTable extends Doctrine_Table {

    //Busca el valor del dato hasta la etapa $etapa_id
    public function findByNombreHastaEtapa($nombre, $etapa_id) {
        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
        if ($etapa) {
            return Doctrine_Query::create()
                            ->from('ObnDatosSeguimiento d, d.Etapa e, e.Tramite t')
                            ->where('d.nombre = ?', $nombre)
                            ->andWhere('t.id = ?', $etapa->tramite_id)
                            ->andWhere('e.id <= ?', $etapa->id)
                            ->orderBy('d.id DESC')
                            ->fetchOne();
        }
        return false;
    }
    
    public function findByNombreObnIdHastaEtapa($nombre, $etapa_id,$obn_id) {
        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
        if ($etapa) {
            return Doctrine_Query::create()
                            ->from('ObnDatosSeguimiento d, d.Etapa e, e.Tramite t')
                            ->where('d.nombre = ?', $nombre)
                            ->andWhere('d.obn_id=?',$obn_id)
                            ->andWhere('t.id = ?', $etapa->tramite_id)
                            ->andWhere('e.id <= ?', $etapa->id)
                            ->orderBy('d.id DESC')
                            ->fetchOne();
        }
        return false;
    }

    //Busca todos los dato hasta la ultima etapa del $tramite_id
    public function findByTramite($tramite_id) {

        $datos_actuales = Doctrine_Query::create()
                ->select('d.id, MAX(d.id) as max_id')
                ->from('ObnDatosSeguimiento d, d.Etapa.Tramite t')
                ->andWhere('t.id = ?', $tramite_id)
                ->groupBy('d.nombre')
                ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        $datos_actuales_ids = array();
        foreach ($datos_actuales as $d)
            $datos_actuales_ids[] = $d['max_id'];

        $datos = Doctrine_Query::create()
                ->from('ObnDatosSeguimiento d, d.Etapa.Tramite t')
                ->andWhere('t.id = ?', $tramite_id)
                ->andWhereIn('d.id', $datos_actuales_ids)
                ->groupBy('d.nombre')
                ->execute();

        return $datos;
    }

    //Devuelve un arreglo con los valores del dato recopilados durante todo el proceso
    public function findGlobalByNombreAndProceso($nombre, $tramite_id) {
        $tramite = Doctrine_Core::getTable('Tramite')->find($tramite_id);

        $datos = Doctrine_Query::create()
                ->from('ObnDatosSeguimiento d, d.Etapa.Tramite t,t.Proceso p')
                ->where('d.nombre = ?', $nombre)
                ->andWhere('p.id = ?', $tramite->proceso_id)
                ->andWhere('t.id != ?', $tramite->id)
                ->having('d.id = MAX(d.id)')
                ->groupBy('t.id')
                ->execute();

        $result = array();
        foreach ($datos as $d)
            $result[] = $d->valor;

        return $result;
    }

    public function findCampoFile($tramite, $nombre) {
//        print_r($tramite);
//        print_r($nombre);
        $datos = Doctrine_Query::create()
                ->select('d.valor')
                ->from('ObnDatosSeguimiento d, d.Etapa.Tramite t')
                ->where('d.nombre = ?', $nombre)
                ->andWhere('t.id = ?', $tramite)
                ->limit(1)
                ->execute();
        $result = array();
        foreach ($datos as $d)
            $result[] = $d->valor;
//        print_r($result);
        return $result;
    }

}