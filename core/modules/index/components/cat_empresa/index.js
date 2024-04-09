import { useState, useEffect, useRef } from 'react';
import 'datatables.net-dt/css/jquery.dataTables.css';
import 'datatables.net';
import { Button, Modal } from 'react-bootstrap';
import axios from 'axios';
import { toast } from 'react-toastify';
import i18n from '../util/lang/i18n';
import { useTranslation } from 'react-i18next';

function CatEmpresaIndex({ empId }) {
    const { t, i18n } = useTranslation();
    const [empresa, setEmpresa] = useState({
        id: null,
        nombre: '',
        identificador_fiscal: '',
        contacto_nombre: '',
        contacto_email: '',
        contacto_prefijo: '',
        contacto_celular: '',
        pais_id: '',
        idioma_id: '',
        estado: '1',
    });
    const [paises, setPaises] = useState([]);
    const [idiomas, setIdiomas] = useState([]);
    
    useEffect(() => {
        actionGetPaises();
        actionGetIdiomas();
        actionGetEmpresaById(empId);
    },[]);

    const handleChange = (e) => {
        const { id, value } = e.target;
        setEmpresa({ ...empresa, [id]: value });
    };

    const handleSubmit = (e) => {
        console.log('empresa')
        console.log(empresa)
        e.preventDefault();
        const formData = new FormData();
        const action = 'cat_empresas_update';

        formData.append('emp_id', empresa.id);
        formData.append('emp_nombre', empresa.nombre);
        formData.append('emp_idfiscal', empresa.identificador_fiscal);
        formData.append('emp_contacto', empresa.contacto_nombre);
        formData.append('emp_cont_email', empresa.contacto_email);
        formData.append('emp_cont_cel', empresa.contacto_celular==''?'':empresa.contacto_prefijo+'-'+empresa.contacto_celular);
        formData.append('pai_id', empresa.pais_id);
        formData.append('idm_id', empresa.idioma_id);
        formData.append('emp_estado', empresa.estado);

        actionUpdateEmpresa(action, formData)
    };
    
    const actionGetPaises = () => {
        axios.get('./?action=cat_paises_get')
        .then((response) => {
            setPaises(response.data.data);
        })
        .catch((error) => {
            console.error('Error al obtener los paises:', error);
        });
    };

    const actionGetIdiomas = () => {
        axios.get('./?action=cat_idiomas_get')
        .then((response) => {
            setIdiomas(response.data.data);
        })
        .catch((error) => {
            console.error('Error al obtener los idiomas:', error);
        });
    };

    const actionGetEmpresaById = async (emp_id) => {
        const formData = new FormData();
        formData.append('emp_id', emp_id);

        axios.post(`?action=cat_empresas_getForId`, formData)
        .then((response) => {
            setEmpresa(response.data.data)
        })
        .catch((error) => {
            toast.error('Ocurrió un error');
        });
    }

    const actionUpdateEmpresa = (action, formData) => {
        axios
        .post(`?action=${action}`, formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          })
        .then((response) => {
            let data = response.data
            if (response.data.substr(0, 1) == 1) {
                toast.success(data.substr(2));
            } else {
                toast.error(data.substr(2));
            }
        })
        .catch((error) => {
            console.error('Error al guardar:', error);
            toast.error('Ocurrió un error');
        });
    }

    return (
        <div className="row">
            <div className="col-12 grid-margin stretch-card">
                <div className="card">
                    <section>
                        <div className="well well-sm"
                            style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '0.5rem', margin: '0.5rem'}}
                            >
                            <h4>{t('empresa.configuracion')}</h4>
                        </div>
                        <div className="p-2">
                            <div className="col-md-12">
                                <form>
                                    <div className="form-group row">
                                        <div className="col-md-8">
                                            <label htmlFor="nombre">{t('empresa.nombre')}</label>
                                            <input
                                                className="form-control form-control-sm"
                                                type="text"
                                                id="nombre"
                                                onChange={handleChange}
                                                value={empresa.nombre}
                                            />
                                        </div>
                                        <div className="col-md-4">
                                            <label htmlFor="identificador_fiscal">{t('empresa.identificador_fiscal')}</label>
                                            <input
                                                className="form-control form-control-sm"
                                                type="text"
                                                id="identificador_fiscal"
                                                autoComplete="false"
                                                onChange={handleChange}
                                                value={empresa.identificador_fiscal}
                                            />
                                        </div>
                                    </div>
                                    <div className="form-group row">
                                        <div className="col-md-6">
                                            <label htmlFor="contacto_nombre">{t('empresa.contacto_nombre')}</label>
                                            <input
                                                className="form-control form-control-sm"
                                                type="text"
                                                id="contacto_nombre"
                                                onChange={handleChange}
                                                value={empresa.contacto_nombre}
                                            />
                                        </div>
                                        <div className="col-md-6">
                                            <label htmlFor="contacto_email">{t('empresa.contacto_email')}</label>
                                            <input
                                                className="form-control form-control-sm"
                                                type="text"
                                                id="contacto_email"
                                                onChange={handleChange}
                                                value={empresa.contacto_email}
                                            />
                                        </div>
                                    </div>
                                    <div className="form-group row">
                                        <div className="col-md-8">
                                            <label htmlFor="celular">{t('empresa.celular')}</label>
                                            <div className="input-group">
                                                <div className="input-group-append">
                                                    <select className="form-control form-control-sm" id="contacto_prefijo" name="contacto_prefijo" value={empresa.contacto_prefijo} onChange={handleChange}>
                                                        <option value="">{t('empresa.seleccione_prefijo')}</option>
                                                        { paises && paises.map((item, index) => (<option key={index} value={item.prefijo}>{item.nombre} (+{item.prefijo})</option>)) }
                                                    </select>
                                                </div>
                                                <input
                                                    className="form-control form-control-sm"
                                                    type="text"
                                                    id="celular"
                                                    onChange={handleChange}
                                                    value={empresa.contacto_celular}
                                                />
                                            </div>
                                        </div>
                                        <div className="col-md-4">
                                            <label htmlFor="menu_id">{t('empresa.pais_nombre')}</label>
                                            <select className="form-control" id="pais_id" name="pais_id" value={empresa.pais_id} onChange={handleChange}>
                                                <option>Seleccione</option>
                                                { paises && paises.map((item, index) => (<option key={index} value={item.id}>{item.nombre}</option>))}
                                            </select>
                                        </div>
                                    </div>
                                    <div className="form-group row">
                                        <div className="col-md-8">
                                            <label htmlFor="menu_id">{t('empresa.idioma_nombre')}</label>
                                            <select className="form-control" id="idioma_id" name="idioma_id" value={empresa.idioma_id} onChange={handleChange}>
                                                <option>Seleccione</option>
                                                {idiomas && idiomas.map((item, index) => (<option key={index} value={item.id}>{item.nombre}</option>))}
                                            </select>
                                        </div>
                                        <div className="col-md-4">
                                            <div className='form-group'>
                                                <label htmlFor="menu_id">{t('empresa.estado')}</label>
                                                <select className="form-control" id="estado" name="estado" value={empresa.estado} onChange={handleChange}>
                                                    <option>Seleccione</option>
                                                    <option value="1">{t('empresa.activa')}</option>
                                                    <option value="2">{t('empresa.inactiva')}</option>
                                                    <option value="0">{t('empresa.estado_prueba')}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <Button variant="primary" onClick={handleSubmit}>
                                        {t('empresa.actualizar')}
                                    </Button>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>        
    );
}

export default CatEmpresaIndex;