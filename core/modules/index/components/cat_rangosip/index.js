import { useState, useEffect, useRef } from 'react';
import 'datatables.net-dt/css/jquery.dataTables.css';
import 'datatables.net';
import { Button, Modal } from 'react-bootstrap';
import axios from 'axios';
import Swal from 'sweetalert2'
import { toast } from 'react-toastify';
import i18n from '../util/lang/i18n';
import { useTranslation } from 'react-i18next';

function CatRangosIpIndex() {
    const { t, i18n } = useTranslation();
    const tableRef = useRef(null);
    const [showModal, setShowModal] = useState(false);
    const [rangoip, setRangoIp] = useState({
        id: null,
        pai_id: '',
        rango_inicial: '',
        rango_final: '',
    });
    const [rangoIpData, setRangoIpData] = useState(null);
    const [paises, setPaises] = useState([]);

    useEffect(() => {
        const table = $(tableRef.current).DataTable({
          ajax: {
            method: 'POST',
            url: './?action=cat_rangosip_get',
            data: { tipo: 1 }
          },
          columns: [
            { data: 'id' },
            { data: 'pai_nombre' },
            { data: 'rango_inicial' },
            { data: 'rango_final' },
            {
                data: null,
                render: function (data, type, row) {
                  return `
                    <div class="btn-group" role="group">
                      <button class="btn btn-sm btn-success btn-edit" title="Editar" role="group"
                        data-id="${row.id}" data-pai_id="${row.pai_id}" data-rango_inicial="${row.rango_inicial}" data-rango_final="${row.rango_final}">
                        <i class="fas fa-edit" aria-hidden="true"></i>
                      </button>
                      <button class="btn btn-sm btn-danger btn-eliminar" title="Eliminar" role="group"
                        data-id="${row.id}">
                        <i class="fas fa-trash" aria-hidden="true"></i>
                      </button>
                    </div>
                  `;
                }
            }
          ],
          language: {
            url: `//cdn.datatables.net/plug-ins/1.10.11/i18n/${t('datatable.idioma')}.json`
          },
        });
        setRangoIpData(table);

        $(tableRef.current).on('click', '.btn-edit', function () {
            setRangoIp({
                id: $(this).data('id'),
                pai_id: $(this).data('pai_id'),
                rango_inicial: $(this).data('rango_inicial'),
                rango_final: $(this).data('rango_final')
            })
            
            setShowModal(true);
        });

        $(tableRef.current).on('click', '.btn-eliminar', function (e) {
            e.preventDefault();
            const rangoIpId = $(this).data('id');
            modalEliminar(table, rangoIpId)
        });        
    }, []);

    useEffect(() => {
        actionGetPaises();
    },[]);

    const modalEliminar = (table, rangoIpId) => {
        Swal.fire({
            title: '¿Seguro que desea eliminar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si',
            cancelButtonText: 'Cancelar'
          }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('id', rangoIpId);
                
                actionDeleteRangoIp(table, formData);
            }
        })
    }

    const handleChange = (e) => {
        const { id, value } = e.target;       
        setRangoIp({ ...rangoip, [id]: value });        
    };    
      
    const handleOpenModal = () => {
        setShowModal(true);
    };

    const handleCloseModal = () => {
        resetForm();
        setShowModal(false);
    };    

    const handleSubmit = (e) => {
        e.preventDefault();
        const formData = new FormData();    
        let action = 'cat_rangosip_set';

        if(rangoip.id != null) {            
            action = 'cat_rangosip_update';
            formData.append('ripId', rangoip.id);
        }

        formData.append('paiId', rangoip.pai_id);
        formData.append('ripRangoInicial', rangoip.rango_inicial);
        formData.append('ripRangoFinal', rangoip.rango_final);

        actionSetUpdateRangoIp(action, formData)
    };

    const resetForm = () => {
        setRangoIp({
            id: null,
            nombre: '',
            estado: "1"
        });
        setShowModal(false);
    };

    const actionGetPaises = () => {
        axios.get('./?action=cat_paises_get')
        .then((response) => {
            setPaises(response.data.data);
        })
        .catch((error) => {
            console.error('Error al obtener los objetos:', error);
        });
    };

    const actionSetUpdateRangoIp = (action, formData) => {
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
                resetForm();
                if (rangoIpData) {
                    rangoIpData.ajax.reload(null, false);
                }
            } else {
                toast.error(data.substr(2));
            }
        })
        .catch((error) => {
            console.error('Error al guardar el pais:', error);
            toast.error('Ocurrió un error');
        });
    }

    const actionDeleteRangoIp = (table, formData) => {
        axios.post(`?action=cat_rangosip_delete`, formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          })
        .then((response) => {
            let data = response.data
            if (response.data.substr(0, 1) == 1) {
                toast.success(data.substr(2));
                if (table) {
                    table.ajax.reload(null, false);
                }
            } else {
                toast.error(data.substr(2));
            }
        })
        .catch((error) => {
            console.error('Error al eliminar el rango ip:', error);
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
                            <h4>{t('rangoip.titulo')}</h4>
                            <div>
                                <button
                                    role="button"
                                    className="btn btn-sm btn-primary"
                                    id="nuevoRangoIp"
                                    onClick={handleOpenModal}
                                >
                                    {t('rangoip.txt_nuevo')}
                                </button>
                            </div>
                        </div>
                        <div>
                            <div className="col-md-12">
                                <table className="display compact" style={{ width: '100%' }} id="table-entidades" ref={tableRef}>
                                    <thead>
                                        <tr>
                                            <th>{t('rangoip.id')}</th>
                                            <th>{t('rangoip.pais_nombre')}</th>
                                            <th>{t('rangoip.rango_inicial')}</th>
                                            <th>{t('rangoip.rango_final')}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <Modal show={showModal} onHide={handleCloseModal}>
                        <Modal.Header>
                            <Modal.Title>{rangoip.id==null?t('rangoip.txt_nuevo'):t('rangoip.txt_editar')}</Modal.Title>
                            <button type="button" className="btn-close custom-close-btn" onClick={handleCloseModal}>
                            &times;
                            </button>
                        </Modal.Header>
                            <Modal.Body>
                                <form>
                                    <div className="form-group">
                                        <label htmlFor="menu_id">{t('rangoip.pais_nombre')}</label>
                                        <select className="form-control" id="pai_id" name="pai_id" value={rangoip.pai_id} onChange={handleChange}>
                                            <option>Seleccione</option>
                                            {paises && paises.map((item, index) => (<option key={index} value={item.id}>{item.nombre}</option>))}
                                        </select>
                                    </div>
                                    <div className="form-group">
                                        <label htmlFor="rango_inicial">{t('rangoip.rango_inicial')}</label>
                                        <input
                                            className="form-control"
                                            type="ip"
                                            id="rango_inicial"
                                            onChange={handleChange}
                                            value={rangoip.rango_inicial}
                                        />
                                    </div>
                                    <div className="form-group">
                                        <label htmlFor="rango_final">{t('rangoip.rango_final')}</label>
                                        <input
                                            className="form-control"
                                            type="ip"
                                            id="rango_final"
                                            onChange={handleChange}
                                            value={rangoip.rango_final}
                                        />
                                    </div>
                                </form>
                            </Modal.Body>
                            <Modal.Footer>
                                <Button variant="secondary" onClick={handleCloseModal}>                    
                                    {t('rangoip.cancelar')}
                                </Button>
                                <Button variant="primary" onClick={handleSubmit}>
                                    {rangoip.id==null?t('rangoip.guardar'):t('rangoip.actualizar')}
                                </Button>
                            </Modal.Footer>
                        </Modal>
                    </section>
                </div>
            </div>
        </div>        
    );
}

export default CatRangosIpIndex;