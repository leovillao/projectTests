import { useState, useEffect, useRef } from 'react';
import 'datatables.net-dt/css/jquery.dataTables.css';
import 'datatables.net';
import { Button, Modal } from 'react-bootstrap';
import axios from 'axios';
import Swal from 'sweetalert2'
import { toast } from 'react-toastify';
import i18n from '../util/lang/i18n';
import { useTranslation } from 'react-i18next';

function CatSubSubcategoriasIndex() {
    const { t, i18n } = useTranslation();
    const tableRef = useRef(null);
    const [showModal, setShowModal] = useState(false);
    const [subcategoria, setSubcategoria] = useState({
        id: null,
        nombre: null,
        cat_id: null,
    });
    const [subcategoriaData, setSubcategoriaData] = useState(null);
    const [categorias, setCategorias] = useState([]);
    const [title, setTitle] = useState ('')

    useEffect(() => {
        const table = $(tableRef.current).DataTable({
          ajax: {
            method: 'POST',
            url: './?action=cat_subcategorias_get',
            data: { tipo: 1 }
          },
          columns: [
            { data: 'id' },
            { data: 'nombre' },
            { data: 'cat_nombre' },
            {
                data: null,
                render: function (data, type, row) {
                  return `
                    <div class="btn-group" role="group">
                      <button class="btn btn-sm btn-success btn-edit-subcategoria" title="Editar" role="group"
                        data-id="${row.id}"  data-nombre="${row.nombre}" data-cat_id="${row.cat_id}">
                        <i class="fas fa-edit" aria-hidden="true"></i>
                      </button>
                      <button class="btn btn-sm btn-danger btn-eliminar-subcategoria" title="Eliminar" role="group"
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
        setSubcategoriaData(table);

        $(tableRef.current).on('click', '.btn-edit-subcategoria', function () {
            setSubcategoria({
                id: $(this).data('id'),
                nombre: $(this).data('nombre'),
                cat_id: $(this).data('cat_id')
            })
            setShowModal(true);
            setTitle($(this).data('nombre'))
        });

        $(tableRef.current).on('click', '.btn-eliminar-subcategoria', function (e) {
            e.preventDefault();
            const subcategoriaId = $(this).data('id');
            const subcategoriaNombre = $(this).data('nombre');
            modalEliminarSubcategoria(table, subcategoriaId, subcategoriaNombre)
        });        
    }, []);

    useEffect(() => {
        actionGetCategorias();
    },[]);

    const modalEliminarSubcategoria = (table, subcategoriaId, subcategoriaNombre) => {
        Swal.fire({
            title: '¿Seguro que desea eliminar?',
            text: `${subcategoriaNombre}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si',
            cancelButtonText: 'Cancelar'
          }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('id', subcategoriaId);
                
                actionDeleteSubcategoria(table, formData);
            }
        })
    }

    const handleChange = (e) => {
        setSubcategoria({ ...subcategoria, [e.target.id]: e.target.value });
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

        let action = '';

        formData.append('nombreSbc', subcategoria.nombre);
        formData.append('idCat', subcategoria.cat_id);

        if(subcategoria.id == null) {
            action = 'cat_subcategorias_set';
        }
        else {
            action = 'cat_subcategorias_update';
            formData.append('idSbc', subcategoria.id);
        }

        actionSetUpdateSubcategoria(action, formData)
    };

    const resetForm = () => {
        setSubcategoria({
            id: null,
            nombre: '',
            cat_id: '',
        });
        setShowModal(false);
    };

    const actionGetCategorias = () => {
        axios.get('./?action=cat_categorias_get')
        .then((response) => {
            setCategorias(response.data.data);            
        })
        .catch((error) => {
            console.error('Error al obtener los objetos:', error);
        });
    };

    const actionSetUpdateSubcategoria = (action, formData) => {
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
                if (subcategoriaData) {
                    subcategoriaData.ajax.reload(null, false);
                }
            } else {
                toast.error(data.substr(2));
            }
        })
        .catch((error) => {
            console.error('Error al guardar:', error);
            toast.error('Ocurrió un error');
        });
    }

    const actionDeleteSubcategoria = (table, formData) => {
        axios.post(`?action=cat_subcategorias_delete`, formData, {
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
                            <h4>{t('subcategoria.titulo')}</h4>
                            <div>
                                <button
                                    role="button"
                                    className="btn btn-sm btn-primary"
                                    id="nuevaSubcategoria"
                                    onClick={handleOpenModal}
                                >
                                   {t('subcategoria.txt_nuevo')}
                                </button>
                            </div>
                        </div>
                        <div>
                            <div className="col-md-12">
                                <table className="display compact" style={{ width: '100%' }} id="table-entidades" ref={tableRef}>
                                    <thead>
                                        <tr>
                                            <th>{t('subcategoria.id')}</th>
                                            <th>{t('subcategoria.nombre')}</th>
                                            <th>{t('subcategoria.categoria_nombre')}</th>
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
                                {subcategoria.id==null?t('subcategoria.txt_nuevo'):t('subcategoria.txt_editar')}
                                <p className="card-description">{title}</p>
                            </Modal.Title>
                            <button type="button" className="btn-close custom-close-btn" onClick={handleCloseModal}>
                            &times;
                            </button>
                        </Modal.Header>
                            <Modal.Body>
                                <form>
                                    <div className="form-group">
                                        <label htmlFor="cat_id">{t('subcategoria.categoria_nombre')}</label>
                                        <select className="form-control" id="cat_id" name="cat_id" value={subcategoria.cat_id} onChange={handleChange}>
                                            <option value="">{t('subcategoria.seleccione')}</option>
                                            { categorias && categorias.map((item) => (<option key={item.id} value={item.id}>{item.nombre}</option>))}
                                        </select>
                                    </div>
                                    <div className="form-group">
                                        <label htmlFor="subcategoriaName">{t('subcategoria.nombre')}</label>
                                        <input
                                            className="form-control"
                                            type="text"
                                            id="nombre"
                                            onChange={handleChange}
                                            value={subcategoria.nombre}
                                        />
                                    </div>                                
                                </form>
                            </Modal.Body>
                            <Modal.Footer>
                                <Button variant="secondary" onClick={handleCloseModal}>                    
                                    {t('subcategoria.cancelar')}
                                </Button>
                                <Button variant="primary" onClick={handleSubmit}>
                                    {subcategoria.id==null?t('subcategoria.guardar'):t('subcategoria.actualizar')}
                                </Button>
                            </Modal.Footer>
                        </Modal>
                    </section>
                </div>
            </div>
        </div>        
    );
}

export default CatSubSubcategoriasIndex;