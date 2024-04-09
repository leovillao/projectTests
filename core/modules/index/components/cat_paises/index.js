import { useState, useEffect, useRef } from 'react';
import 'datatables.net-dt/css/jquery.dataTables.css';
import 'datatables.net';
import { Button, Modal } from 'react-bootstrap';
import axios from 'axios';
import Swal from 'sweetalert2'
import { toast } from 'react-toastify';
import i18n from '../util/lang/i18n';
import { useTranslation } from 'react-i18next';

function CatPaisesIndex() {
    const { t, i18n } = useTranslation();
    const tableRef = useRef(null);
    const [showModal, setShowModal] = useState(false);
    const [pais, setPais] = useState({
        id: null,
        codigo: '',
        nombre: '',
        prefijo: ''
    });
    const [paisData, setPaisData] = useState(null);
    const [title, setTitle] = useState ('')

    useEffect(() => {
        const table = $(tableRef.current).DataTable({
          ajax: {
            method: 'POST',
            url: './?action=cat_paises_get',
            data: { tipo: 1 }
          },
          columns: [
            { data: 'id' },
            { data: 'codigo' },
            { data: 'nombre' },
            { data: 'prefijo' },
            {
                data: null,
                render: function (data, type, row) {
                  return `
                    <div class="btn-group" role="group">
                      <button class="btn btn-sm btn-success btn-edit-pais" title="Editar" role="group"
                        data-id="${row.id}" data-codigo="${row.codigo}" data-nombre="${row.nombre}" data-prefijo="${row.prefijo}">
                        <i class="fas fa-edit" aria-hidden="true"></i>
                      </button>
                      <button class="btn btn-sm btn-danger btn-eliminar-pais" title="Eliminar" role="group"
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
        setPaisData(table);

        $(tableRef.current).on('click', '.btn-edit-pais', function () {
            setPais({
                id: $(this).data('id'),
                codigo: $(this).data('codigo'),
                nombre: $(this).data('nombre'),
                prefijo: $(this).data('prefijo')
            })
            setTitle($(this).data('nombre'));
            setShowModal(true);
        });

        $(tableRef.current).on('click', '.btn-eliminar-pais', function (e) {
            e.preventDefault();
            const idiomaId = $(this).data('id');
            const idiomaNombre = $(this).data('nombre');
            modalEliminarPais(table, idiomaId, idiomaNombre)
        });        
    }, []);

    const modalEliminarPais = (table, idiomaId, idiomaNombre) => {
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
                
                actionDeletePais(table, formData);
            }
        })
    }

    const handleChange = (e) => {
        const { id, value, type, checked } = e.target;
       
        if(type == 'checkbox') {
            setPais({ ...pais, [id]: checked });
        }
        else {
            setPais({ ...pais, [id]: value });
        }
    };
      
    const handleOpenModal = () => {
        setShowModal(true);
        setTitle('');
    };

    const handleCloseModal = () => {
        resetForm();
        setShowModal(false);
    };    

    const handleSubmit = (e) => {
        e.preventDefault();
        const formData = new FormData();    
        let action = 'cat_paises_set';

        if(pais.id != null) {            
            action = 'cat_paises_update';
            formData.append('paiId', pais.id);
        }

        formData.append('paiCodigo', pais.codigo);
        formData.append('paiNombre', pais.nombre);
        formData.append('paiPrefijo', pais.prefijo);

        actionSetUpdatePais(action, formData)
    };

    const resetForm = () => {
        setPais({
            id: null,
            codigo: '',
            nombre: '',
            estado: "1"
        });
        setShowModal(false);
    };

    const actionSetUpdatePais = (action, formData) => {
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
                if (paisData) {
                    paisData.ajax.reload(null, false);
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

    const actionDeletePais = (table, formData) => {
        axios.post(`?action=cat_paises_delete`, formData, {
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
            console.error('Error al eliminar el pais:', error);
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
                            <h4>{t('pais.titulo')}</h4>
                            <div>
                                <button
                                    role="button"
                                    className="btn btn-sm btn-primary"
                                    id="nuevaPais"
                                    onClick={handleOpenModal}
                                >
                                    {t('pais.txt_nuevo')}
                                </button>
                            </div>
                        </div>
                        <div>
                            <div className="col-md-12">
                                <table className="display compact" style={{ width: '100%' }} id="table-entidades" ref={tableRef}>
                                    <thead>
                                        <tr>
                                            <th>{t('pais.id')}</th>
                                            <th>{t('pais.codigo')}</th>
                                            <th>{t('pais.nombre')}</th>
                                            <th>{t('pais.prefijo')}</th>
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
                                {pais.id==null?t('pais.txt_nuevo'):t('pais.txt_editar')}
                                <p className="card-description">{title}</p>
                            </Modal.Title>
                            <button type="button" className="btn-close custom-close-btn" onClick={handleCloseModal}>
                            &times;
                            </button>
                        </Modal.Header>
                            <Modal.Body>
                                <form>
                                    <div className="form-group">
                                        <label htmlFor="codigo">{t('pais.codigo')}</label>
                                        <input
                                            className="form-control"
                                            type="text"
                                            id="codigo"
                                            onChange={handleChange}
                                            value={pais.codigo}
                                        />
                                    </div>
                                    <div className="form-group">
                                        <label htmlFor="nombre">{t('pais.nombre')}</label>
                                        <input
                                            className="form-control"
                                            type="text"
                                            id="nombre"
                                            onChange={handleChange}
                                            value={pais.nombre}
                                        />
                                    </div>
                                    <div className="form-group">
                                        <label htmlFor="prefijo">{t('pais.prefijo')}</label>
                                        <input
                                            className="form-control"
                                            type="number"
                                            id="prefijo"
                                            onChange={handleChange}
                                            value={pais.prefijo}
                                        />
                                    </div>
                                </form>
                            </Modal.Body>
                            <Modal.Footer>
                                <Button variant="secondary" onClick={handleCloseModal}>                    
                                    {t('pais.cancelar')}
                                </Button>
                                <Button variant="primary" onClick={handleSubmit}>
                                    {pais.id==null?t('pais.guardar'):t('pais.actualizar')}
                                </Button>
                            </Modal.Footer>
                        </Modal>
                    </section>
                </div>
            </div>
        </div>        
    );
}

export default CatPaisesIndex;