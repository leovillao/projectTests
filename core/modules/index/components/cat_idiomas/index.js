import { useState, useEffect, useRef } from 'react';
import 'datatables.net-dt/css/jquery.dataTables.css';
import 'datatables.net';
import { Button, Modal } from 'react-bootstrap';
import axios from 'axios';
import Swal from 'sweetalert2'
import { toast } from 'react-toastify';
import i18n from '../util/lang/i18n';
import { useTranslation } from 'react-i18next';

function CatIdiomasIndex() {
    const { t, i18n } = useTranslation();
    const tableRef = useRef(null);
    const [showModal, setShowModal] = useState(false);
    const [idioma, setIdioma] = useState({
        id: null,
        nombre: '',
        estado: true
    });
    const [idiomaData, setIdiomaData] = useState(null);
    const [title, setTitle] = useState ('')

    useEffect(() => {
        const table = $(tableRef.current).DataTable({
          ajax: {
            method: 'POST',
            url: './?action=cat_idiomas_get',
            data: { tipo: 1 }
          },
          columns: [
            { data: 'id' },
            { data: 'nombre' },
            { data: 'codigo' },
            {
                data: 'estado',
                render: function (data) {
                    return data=='1'?t('idioma.activo'):t('idioma.inactivo');
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                  return `
                    <div class="btn-group" role="group">
                      <button class="btn btn-sm btn-success btn-edit-idioma" title="Editar" role="group"
                        data-id="${row.id}" data-nombre="${row.nombre}" data-codigo="${row.codigo}" data-estado="${row.estado=='1'?true:false}">
                        <i class="fas fa-edit" aria-hidden="true"></i>
                      </button>
                      <button class="btn btn-sm btn-danger btn-eliminar-idioma" title="Eliminar" role="group"
                        data-id="${row.id}" data-nombre="${row.nombre}">
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
        setIdiomaData(table);

        $(tableRef.current).on('click', '.btn-edit-idioma', function () {
            setIdioma({
                id: $(this).data('id'),
                nombre: $(this).data('nombre'),
                codigo: $(this).data('codigo'),
                estado: $(this).data('estado')
            })
            setTitle($(this).data('nombre'))
            setShowModal(true);
        });

        $(tableRef.current).on('click', '.btn-eliminar-idioma', function (e) {
            e.preventDefault();
            const idiomaId = $(this).data('id');
            const idiomaNombre = $(this).data('nombre');
            modalEliminarIdioma(table, idiomaId, idiomaNombre)
        });        
    }, []);

    const modalEliminarIdioma = (table, idiomaId, idiomaNombre) => {
        Swal.fire({
            title: '¿Seguro que desea eliminar?',
            text: `${idiomaNombre}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si',
            cancelButtonText: 'Cancelar'
          }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('id', idiomaId);
                
                actionDeleteIdioma(table, formData);
            }
        })
    }

    const handleChange = (e) => {
        const { id, value, type, checked } = e.target;
       
        if(type == 'checkbox') {
            setIdioma({ ...idioma, [id]: checked });
        }
        else {
            setIdioma({ ...idioma, [id]: value });
        }
    };    
      
    const handleOpenModal = () => {
        setShowModal(true);
        setTitle(true);
    };

    const handleCloseModal = () => {
        resetForm();
        setShowModal(false);
    };    

    const handleSubmit = (e) => {
        e.preventDefault();
        const formData = new FormData();    
        let action = 'cat_idiomas_set';

        if(idioma.id != null) {            
            action = 'cat_idiomas_update';
            formData.append('idmId', idioma.id);
        }

        formData.append('idmNombre', idioma.nombre);
        formData.append('idmCodigo', idioma.codigo);
        formData.append('idmEstado', idioma.estado?'1':'0');

        actionSetUpdateIdioma(action, formData)
    };

    const resetForm = () => {
        setIdioma({
            id: null,
            nombre: '',
            estado: "1"
        });
        setShowModal(false);
    };

    const actionSetUpdateIdioma = (action, formData) => {
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
                if (idiomaData) {
                    idiomaData.ajax.reload(null, false);
                }
            } else {
                toast.error(data.substr(2));
            }            
        })
        .catch((error) => {
            console.error('Error al guardar el idioma:', error);
            toast.error('Ocurrió un error');
        });
    }

    const actionDeleteIdioma = (table, formData) => {
        axios.post(`?action=cat_idiomas_delete`, formData, {
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
            console.error('Error al eliminar el idioma:', error);
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
                            <h4>{t('idioma.titulo')}</h4>
                            <div>
                                <button
                                    role="button"
                                    className="btn btn-sm btn-primary"
                                    id="nuevaIdioma"
                                    onClick={handleOpenModal}
                                >
                                    {t('idioma.txt_nuevo')}
                                </button>
                            </div>
                        </div>
                        <div>
                            <div className="col-md-12">
                                <table className="display compact" style={{ width: '100%' }} id="table-entidades" ref={tableRef}>
                                    <thead>
                                        <tr>
                                            <th>{t('idioma.id')}</th>
                                            <th>{t('idioma.nombre')}</th>
                                            <th>{t('idioma.codigo')}</th>
                                            <th>{t('idioma.estado')}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <Modal show={showModal} onHide={handleCloseModal}>
                        <Modal.Header>
                            <Modal.Title>
                                {idioma.id==null?t('idioma.txt_nuevo'):t('idioma.txt_editar')}
                                <p className="card-description">{title}</p>
                            </Modal.Title>
                            <button type="button" className="btn-close custom-close-btn" onClick={handleCloseModal}>
                            &times;
                            </button>
                        </Modal.Header>
                            <Modal.Body>
                                <form>
                                    <div className="form-group">
                                        <label htmlFor="nombre">{t('idioma.nombre')}</label>
                                        <input
                                            className="form-control"
                                            type="text"
                                            id="nombre"
                                            onChange={handleChange}
                                            value={idioma.nombre}
                                        />
                                    </div>
                                    <div className="form-group">
                                        <label htmlFor="codigo">{t('idioma.codigo')} {t('idioma.codigo_logitud')}</label>
                                        <input
                                            className="form-control"
                                            type="text"
                                            id="codigo"
                                            onChange={handleChange}
                                            value={idioma.codigo}
                                            maxLength={2}
                                        />
                                    </div>
                                    <div className='form-group'>
                                        <p className="mb-2">{t('idioma.estado')}</p>
                                        <label className="toggle-switch toggle-switch-success">
                                            <input id="estado" type="checkbox" checked={idioma.estado} onChange={handleChange} />
                                            <span className="toggle-slider round"></span>
                                        </label>
                                    </div>
                                </form>
                            </Modal.Body>
                            <Modal.Footer>
                                <Button variant="secondary" onClick={handleCloseModal}>                    
                                    {t('idioma.cancelar')}
                                </Button>
                                <Button variant="primary" onClick={handleSubmit}>
                                    {idioma.id==null?t('idioma.guardar'):t('idioma.actualizar')}
                                </Button>
                            </Modal.Footer>
                        </Modal>
                    </section>
                </div>
            </div>
        </div>        
    );
}

export default CatIdiomasIndex;