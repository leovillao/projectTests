<?php

class FilesData
{

    public static $Conx = "";

    public static $tablename = "sgn_files";

    public function __construct()
    {
        $this->fil_id = "";
        $this->cat_id = "";
        $this->fil_descripcion = "";
        $this->fil_ubicacion = "";
        $this->fil_certificar = "";
        $this->usr_id = "";
        $this->fil_upload = "";
        $this->fil_hash = "";
        $this->fil_estado = "";
    }

    public static function CerrarTransaccion($conx)
    {
        $ObjExe = new Executor();
        $ObjExe::$conx2 = $conx;
        // $ObjExe->CerrarTransaction();
        return $ObjExe->CloseTransaction();
    }

    public static function CancelarTransaccion($conx)
    {
        $ObjExe = new Executor();
        $ObjExe::$conx2 = $conx;
        // $ObjExe->CerrarTransaction();
        return $ObjExe->BackTransaction();
    }

    public function addFactIngreso($Conx)
    {
        $sql = " insert into " . self::$tablename . " (tpDoc,user_id,fi_sentido,em_ruc,fi_er_ruc,fi_er_name,fi_er_comercial,fi_tipo,fi_docum,fi_ptoemi,fi_codestab,fi_direstab,fi_fechadoc,fi_subtotal,fi_ivasi,fi_ivano,fi_iva,fi_totaldoc,fi_estado,fi_revisado,fi_anio,fi_mes,sucursal_id,prid,fi_porDesc,fi_desc,fi_descDet,fi_desct,fi_totalDescuento,fuid,ccid,negoid,fi_neto,fi_glosa,fi_plazo,fi_cuotas,veid,fi_claveacceso) value ($this->tpDoc,$this->user_id,\"$this->fi_sentido\",\"$this->em_ruc\",\"$this->fi_er_ruc\",\"$this->fi_er_name\",\"$this->fi_er_comercial\",\"$this->fi_tipo\",$this->fi_docum,$this->fi_ptoemi,$this->fi_codestab,\"$this->fi_direstab\",\"$this->fi_fechadoc\",$this->fi_subtotal,$this->fi_ivasi,$this->fi_ivano,$this->fi_iva,$this->fi_totaldoc,$this->fi_estado,$this->fi_revisado,\"$this->fi_anio\",\"$this->fi_mes\",$this->sucursal_id,$this->prid,$this->fi_porDesc,$this->fi_desc,$this->fi_descDet,$this->fi_desct,$this->fi_totalDescuento,$this->fuid,$this->ccid,$this->negoid,$this->fi_neto,$this->fi_glosa,$this->fi_plazo,$this->fi_cuotas,$this->veid,$this->fi_claveacceso)";
        return Executor::doit($sql, $Conx);
//    return $sql;
    }

    public static function AbrirTransaccion()
    {
        $ObjExe = new Executor();
        $ObjExe->OpenTransaction();
        self::$Conx = $ObjExe::$conx2;
        return $ObjExe::$transac;
    }

    public function addFactT_POS($Conx)
    {
        $sql = " insert into " . self::$tablename . " (box_id,user_id,fi_sentido,em_ruc,fi_er_ruc,fi_er_name,fi_er_comercial,fi_tipo,fi_docum,fi_ptoemi,fi_codestab,fi_direstab,fi_fechadoc,fi_subtotal,fi_ivasi,fi_ivano,fi_iva,fi_totaldoc,fi_estado,fi_revisado,fi_anio,fi_mes,sucursal_id,ceid,fi_porDesc,fi_desc,fuid,ccid,negoid,fi_neto,fi_glosa,fpid,veid,fi_claveacceso,fi_valDevo) value ($this->box_id,$this->user_id,\"$this->fi_sentido\",\"$this->em_ruc\",\"$this->fi_er_ruc\",\"$this->fi_er_name\",\"$this->fi_er_comercial\",\"$this->fi_tipo\",\"$this->fi_docum\",\"$this->fi_ptoemi\",\"$this->fi_codestab\",\"$this->fi_direstab\",\"$this->fi_fechadoc\",$this->fi_subtotal,$this->fi_ivasi,$this->fi_ivano,$this->fi_iva,$this->fi_totaldoc,$this->fi_estado,$this->fi_revisado,\"$this->fi_anio\",\"$this->fi_mes\",$this->sucursal_id,$this->ceid,$this->fi_porDesc,$this->fi_desc,$this->fuid,$this->ccid,$this->negoid,$this->fi_neto,$this->fi_glosa,$this->fpid,$this->veid,$this->fi_claveacceso,$this->fi_valDevo)";
        return Executor::doit_T($sql, $Conx);
//    return $sql;
    }

    public static function delById($id)
    {
        $sql = "delete from " . self::$tablename . " where fi_id=$id";
        Executor::doit($sql);
    }

    public function del()
    {
        $sql = "delete from " . self::$tablename . " where fi_id=$this->id";
        Executor::doit($sql);
    }

    public function updateF($Conx)
    {
        $sql = "update " . self::$tablename . " set fi_idfile = $this->fi_idfile where fi_id=$this->fi_id";
        return Executor::doit_T($sql, $Conx);
    }


    public function update()
    {
        $sql = "update " . self::$tablename . " set name=\"$this->name\",lastname=\"$this->lastname\",address=\"$this->address\",phone=\"$this->phone\",email=\"$this->email\",rango_id=$this->rango_id,c1_fullname=\"$this->c1_fullname\",c1_address=\"$this->c1_address\",c1_phone=\"$this->c1_phone\",c1_note=\"$this->c1_note\" where id=$this->id";
        Executor::doit($sql);
    }

    public function addeRet($Conx)
    {
        $sql = "insert into " . self::$tablename . " (sucursal_id,`fi_sentido`,  `user_id` , `em_ruc` , `fi_er_ruc` , `fi_er_name` , `fi_er_comercial` ,`fi_tipo`, `fi_docum` , `fi_codestab`,`fi_ptoemi` , `fi_direstab` , `fi_fechadoc`, `fi_anio` , `fi_mes`, `fi_retfte` , `fi_retiva` , `fi_estado` , `fi_revisado`, `fi_docrel` , `fi_fecharel`, `fi_tiporel`) VALUES ($this->sucursal_id,'E','" . $this->user_id . "','" . $this->em_ruc . "','" . $this->fi_er_ruc . "','" . $this->fi_er_name . "','" . $this->fi_er_comercial . "','07','" . $this->fi_docum . "','" . $this->fi_codestab . "','" . $this->fi_ptoemi . "','" . $this->fi_direstab . "','" . $this->fi_fechadoc . "','" . $this->fi_anio . "','" . $this->fi_mes . "'," . $this->fi_retfte . "," . $this->fi_retiva . ",1,1,'" . $this->fi_docrel . "','" . $this->fi_fecharel . "','" . $this->fi_tiporel . "')";
        return Executor::doit_T($sql, $Conx);
    }


    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where fi_id=$id";
        $query = Executor::doit($sql);
        return Model::many($query[0], new FilesData());
    }

    public static function getAll()
    {
        $sql = "select * from " . self::$tablename . " ";
        $query = Executor::doit($sql);
        return Model::many($query[0], new FilesData());
    }


    public function getUnreads()
    {
        return MessageData::getUnreadsByClientId($this->id);
    }


    // obtiene los documentos relacionados con una categoria
    public static function getByCatId($catid)
    {
        $sql = "select * from " . self::$tablename . " where cat_id=$catid ";
        $query = Executor::doit($sql);
        return Model::many($query[0], new FilesData());
    }
}
