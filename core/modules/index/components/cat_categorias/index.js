import { useState, useEffect, useRef } from 'react';
import 'datatables.net-dt/css/jquery.dataTables.css';
import 'datatables.net';
import { Button, Modal } from 'react-bootstrap';
import axios from 'axios';
import Swal from 'sweetalert2'
import { toast } from 'react-toastify';
import i18n from '../util/lang/i18n';
import { useTranslation } from 'react-i18next';

function CatCategoriasIndex() {
    const { t, i18n } = useTranslation();
    const tableRef = useRef(null);
    const [showModal, setShowModal] = useState(false);
    const [categoria, setCategoria] = useState({
        id: null,
        nombre: ''
    });
    const [categoriaData, setCategoriaData] = useState(null);
    const [title, setTitle] = useState ('')

    useEffect(() => {
        const table = $(tableRef.current).DataTable({
          ajax: {
            method: 'POST',
            url: './?action=cat_categorias_get',
            data: { tipo: 1 }
          },
          columns: [
            { data: 'id' },
            { data: 'nombre' },
            {
                data: null,
                render: function (data, type, row) {
                  return `
                    <div class="btn-group" role="group">
                      <button class="btn btn-sm btn-success btn-edit-categoria" title="Editar" role="group"
                        data-id="${row.id}"  data-nombre="${row.nombre}">
                        <i class="fas fa-edit" aria-hidden="true"></i>
                      </button>
                      <button class="btn btn-sm btn-danger btn-eliminar-categoria" title="Eliminar" role="group"
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
          }
        });
        setCategoriaData(table);

        $(tableRef.current).on('click', '.btn-edit-categoria', function () {
            setCategoria({
                id: $(this).data('id'),
                nombre: $(this).data('nombre')
            })
            setTitle($(this).data('nombre'))
            setShowModal(true);
        });

        $(tableRef.current).on('click', '.btn-eliminar-categoria', function (e) {
            e.preventDefault();
            const categoriaId = $(this).data('id');
            const categoriaNombre = $(this).data('nombre');
            modalEliminarCategoria(table, categoriaId, categoriaNombre)
        });        
    }, []);

    const modalEliminarCategoria = (table, categoriaId, categoriaNombre) => {
        Swal.fire({
            title: '¿Seguro que desea eliminar?',
            text: `${categoriaNombre}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si',
            cancelButtonText: 'Cancelar'
          }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('id', categoriaId);
                
                actionDeleteCategoria(table, formData);
            }
        })
    }

    const handleChange = (e) => {
        setCategoria({ ...categoria, [e.target.id]: e.target.value });
    };    
      
    const handleOpenModal = () => {
        setTitle('');
        setShowModal(true);
    };

    const handleCloseModal = () => {
        resetForm();
        setShowModal(false);
    };    

    const handleSubmit = (e) => {
        e.preventDefault();
        const formData = new FormData();    
        let action = 'cat_categorias_set';

        if(categoria.id == null) {            
            formData.append('nombreCat', categoria.nombre);
        }
        else {
            action = 'cat_categorias_update';
            formData.append('idCategoria', categoria.id);
            formData.append('nombreCat', categoria.nombre);
        }

        actionSetUpdateCategoria(action, formData)
    };

    const resetForm = () => {
        setCategoria({
            id: null,
            nombre: '',
        });
        setShowModal(false);
    };

    const actionSetUpdateCategoria = (action, formData) => {
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
                if (categoriaData) {
                    categoriaData.ajax.reload(null, false);
                }
            } else {
                toast.error(data.substr(2));
            }
        })
        .catch((error) => {
            console.error('Error al guardar la categoría:', error);
            toast.error('Ocurrió un error');
        });
    }

    const actionDeleteCategoria = (table, formData) => {
        axios.post(`?action=cat_categorias_delete`, formData, {
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
            console.error('Error al guardar la categoría:', error);
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
                            <h4>{t('categoria.titulo')}</h4>
                            <div>
                                <button
                                    role="button"
                                    className="btn btn-sm btn-primary"
                                    id="nuevaCategoria"
                                    onClick={handleOpenModal}
                                >
                                   {t('categoria.txt_nuevo')}
                                </button>
                            </div>
                        </div>
                        <div>
                            <div className="col-md-12">
                                <table className="display compact" style={{ width: '100%' }} id="table-entidades" ref={tableRef}>
                                    <thead>
                                        <tr>
                                        <th>{t('categoria.id')}</th>
                                        <th>{t('categoria.nombre')}</th>
                                        <th></th>
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
                                    {categoria.id==null?t('categoria.txt_nuevo'):t('categoria.txt_editar')}
                                    <p className="card-description">{title}</p>
                                </Modal.Title>
                                <button type="button" className="btn-close custom-close-btn" onClick={handleCloseModal}>
                                &times;
                                </button>
                            </Modal.Header>
                            <Modal.Body>
                                <form>
                                    <div className="form-group">
                                        <label htmlFor="categoryName">{t('categoria.nombre')}</label>
                                        <input
                                            className="form-control"
                                            type="text"
                                            id="nombre"
                                            onChange={handleChange}
                                            value={categoria.nombre}
                                        />
                                    </div>
                                </form>
                            </Modal.Body>
                            <Modal.Footer>
                                <Button variant="secondary" onClick={handleCloseModal}>
                                    {t('categoria.cancelar')}
                                </Button>
                                <Button variant="primary" onClick={handleSubmit}>
                                    {categoria.id==null?t('categoria.guardar'):t('categoria.actualizar')}
                                </Button>
                            </Modal.Footer>
                        </Modal>
                    </section>
                </div>
            </div>
        </div>        
    );
}

export default CatCategoriasIndex;