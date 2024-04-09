import { useState, useEffect, useRef } from 'react';
import 'datatables.net-dt/css/jquery.dataTables.css';
import 'datatables.net';
import { Button, Modal } from 'react-bootstrap';
import axios from 'axios';
import Swal from 'sweetalert2'
import { toast } from 'react-toastify';
import i18n from '../util/lang/i18n';
import { useTranslation } from 'react-i18next';

function CatObjetosIndex() {
    const { t, i18n } = useTranslation();
    const tableRef = useRef(null);
    const [showModal, setShowModal] = useState(false);
    const [showModalTraduccion, setShowModalTraduccion] = useState(false);
    const [objeto, setObjeto] = useState({
        id: null,
        codigo: null,
        nombre: null,
        menu_id: null,
    });
    const [objetoData, setObjetoData] = useState(null);
    const [traducciones, setTraducciones] = useState([]);
    const [menuList, setMenuList] = useState([]);
    
    useEffect(() => {
        const table = $(tableRef.current).DataTable({
          ajax: {
            method: 'POST',
            url: './?action=cat_objetos_get',
            data: { tipo: 1 }
          },
          columns: [
            { data: 'id' },
            { data: 'codigo' },
            { data: 'nombre' },
            { data: 'men_nombre' },
            {
                data: null,
                render: function (data, type, row) {
                  return `
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-info btn-traduccion" title="Traducción" role="group"
                        data-id="${row.id}" data-codigo="${row.codigo}">
                        <i class="fas fa-book" aria-hidden="true"></i>
                      </button>
                      <button class="btn btn-sm btn-success btn-edit" title="Editar" role="group"
                        data-id="${row.id}" data-codigo="${row.codigo}" data-menu_id="${row.men_id}" data-nombre="${row.nombre}">
                        <i class="fas fa-edit" aria-hidden="true"></i>
                      </button>
                      <button class="btn btn-sm btn-danger btn-eliminar" title="Eliminar" role="group"
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
        setObjetoData(table);

        $(tableRef.current).on('click', '.btn-traduccion', function () {
            setObjeto({
                id: $(this).data('id'),
                codigo: $(this).data('codigo'),
            })            

            actionGetTraducctiones($(this).data('id'));
        }); 

        $(tableRef.current).on('click', '.btn-edit', function () {
            setObjeto({
                id: $(this).data('id'),
                codigo: $(this).data('codigo'),
                nombre: $(this).data('nombre'),
                menu_id: $(this).data('menu_id')
            })
            
            setShowModal(true);
        });

        $(tableRef.current).on('click', '.btn-eliminar', function (e) {
            e.preventDefault();
            const objetoId = $(this).data('id');
            const objetoNombre = $(this).data('nombre');
            modalEliminarObjeto(table, objetoId, objetoNombre)
        });   

    }, []);

    const handleInputChange = (vwi_idm, idm_id, valor) => {
        const traducciones_ = traducciones.map((elemento) => {
          if (elemento.vwi_idm === vwi_idm && elemento.idm_id == idm_id) {
            return { ...elemento, vot_texto: valor };
          }
          return elemento;
        });
    
        setTraducciones(traducciones_);
      };

    const formTraducciones = () => {        
        return traducciones && traducciones.map((item, index) => (
            <div className="form-group row" key={index}>
                <label className="col-sm-2 col-form-label">{item.idm_nombre}</label>
                <div className="col-sm-10">
                    <div className="input-group">
                        <input type="text" className="form-control"
                            value={item.vot_texto}
                            onChange={(e) => handleInputChange(item.vwi_idm, item.idm_id, e.target.value)}/>
                        <div className="input-group-append">
                            <button className="btn btn-sm btn-info" type="button"
                            onClick={() => actionSetTraduccion(item.vwi_id, item.idm_id, item.vot_texto)}><i className="fa fa-save"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        ))
    };

    useEffect(() => {
        actionGetMenu();
    },[]);

    useEffect(() => {
        if(traducciones.length > 0) {
            formTraducciones()
        }
    }, [traducciones]);

    const modalEliminarObjeto = (table, objetoId, objetoNombre) => {
        Swal.fire({
            title: '¿Seguro que desea eliminar?',
            text: `${objetoNombre}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si',
            cancelButtonText: 'Cancelar'
          }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('id', objetoId);
                
                actionDeleteObjeto(table, formData);
            }
        })
    }

    const handleChange = (e) => {
        const { id, value } = e.target;
        setObjeto({ ...objeto, [id]: value });
    };    
      
    const handleOpenModal = () => {
        setShowModal(true);
    };

    const handleCloseModal = () => {
        resetForm();
        setShowModal(false);
    };

    const handleCloseModalTraduccion = () => {
        resetForm();
        setShowModalTraduccion(false);
    };    

    const handleSubmit = (e) => {
        e.preventDefault();
        const formData = new FormData();    
        let action = 'cat_objetos_set';

        if(objeto.id != null) {
            action = 'cat_objetos_update';
            formData.append('vwiId', objeto.id);
        }

        formData.append('vwiNombre', objeto.nombre);
        formData.append('vwiCodigo', objeto.codigo);
        formData.append('menId', objeto.menu_id);

        actionSetUpdateObjeto(action, formData)
    };

    const resetForm = () => {
        setObjeto({
            id: null,
            codigo: null,
            nombre: null,
            menu_id: null,
        });
        setShowModal(false);
    };

    const actionGetMenu = () => {
        axios.get('./?action=cat_menu_get')
        .then((response) => {
            setMenuList(response.data.data);            
        })
        .catch((error) => {
            console.error('Error al obtener los objetos:', error);
        });
    };

    const actionGetTraducctiones = async (vwi_id) => {
        const formData = new FormData();
        formData.append('vwi_id', vwi_id);

        axios.post(`?action=traducciones_getForObjecto`, formData)
        .then((response) => {
            console.log(response.data);
            setShowModalTraduccion(true);
            setTraducciones(response.data.data)
        })
        .catch((error) => {
            console.error('Error al eliminar el objeto:', error);
            toast.error('Ocurrió un error');
        });
    }

    const actionSetTraduccion = (vwi_id, idm_id, vot_texto) => {
        const formData = new FormData();
        formData.append('vwi_id', vwi_id);
        formData.append('idm_id', idm_id);
        formData.append('vot_texto', vot_texto);

        axios.post('./?action=traducciones_set', formData)
        .then((response) => {
            let data = response.data
            if (response.data.substr(0, 1) == 1) {
                toast.success(data.substr(2));
            } else {
                toast.error(data.substr(2));
            }
        })
        .catch((error) => {
            console.error('Error al obtener los objetos:', error);
        });
    };

    const actionSetUpdateObjeto = (action, formData) => {
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
                if (objetoData) {
                    objetoData.ajax.reload(null, false);
                }
            } else {
                toast.error(data.substr(2));
            }
        })
        .catch((error) => {
            console.error('Error al guardar el objeto:', error);
            toast.error('Ocurrió un error');
        });
    }

    const actionDeleteObjeto = (table, formData) => {
        axios.post(`?action=cat_objetos_delete`, formData, {
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
            console.error('Error al eliminar el objeto:', error);
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
                            <h4>{t('objeto.titulo')}</h4>
                            <div>
                                <button
                                    role="button"
                                    className="btn btn-sm btn-primary"
                                    id="nuevoObjeto"
                                    onClick={handleOpenModal}
                                >
                                    {t('objeto.txt_nuevo')}
                                </button>
                            </div>
                        </div>
                        <div>
                            <div className="col-md-12">
                                <table className="display compact" style={{ width: '100%' }} id="table-entidades" ref={tableRef}>
                                    <thead>
                                        <tr>
                                            <th>{t('objeto.id')}</th>
                                            <th>{t('objeto.codigo')}</th>
                                            <th>{t('objeto.nombre')}</th>
                                            <th>{t('objeto.nombre_menu')}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <Modal show={showModalTraduccion} onHide={handleCloseModalTraduccion} dialogClassName="modal-lg">
                            <Modal.Header>
                                <Modal.Title>{t('objeto.editar_traduccion')}: {objeto.codigo}</Modal.Title>
                                <button type="button" className="btn-close custom-close-btn" onClick={handleCloseModalTraduccion}>
                                &times;
                                </button>
                            </Modal.Header>
                            <Modal.Body>
                                <form>
                                    {formTraducciones()}
                                </form>
                            </Modal.Body>
                            <Modal.Footer>
                                <Button variant="secondary" onClick={handleCloseModalTraduccion}>                    
                                {t('objeto.txt_cerrar')}
                                </Button>                                
                            </Modal.Footer>
                        </Modal>
                        <Modal show={showModal} onHide={handleCloseModal}>
                            <Modal.Header>
                                <Modal.Title>{objeto.id==null?t('objeto.txt_nuevo'):t('objeto.txt_editar')}</Modal.Title>
                                <button type="button" className="btn-close custom-close-btn" onClick={handleCloseModal}>
                                &times;
                                </button>
                            </Modal.Header>
                            <Modal.Body>
                                <form>
                                    <div className="form-group">
                                        <label htmlFor="menu_id">{t('objeto.nombre_menu')}</label>
                                        <select className="form-control" id="menu_id" name="menu_id" value={objeto.menu_id} onChange={handleChange}>
                                            <option>Seleccione</option>
                                            {menuList && menuList.map((item, index) => (<option key={index} value={item.id}>{item.nombre}</option>))}
                                        </select>
                                    </div>
                                    <div className="form-group">
                                        <label htmlFor="nombre">{t('objeto.codigo')} ({t('objeto.codigo_placeholder')})</label>
                                        <input
                                            className="form-control"
                                            type="text"
                                            id="codigo"
                                            onChange={handleChange}
                                            value={objeto.codigo}
                                        />
                                    </div>
                                    <div className="form-group">
                                        <label htmlFor="nombre">{t('objeto.descripcion')}</label>
                                        <input
                                            className="form-control"
                                            type="text"
                                            id="nombre"
                                            onChange={handleChange}
                                            value={objeto.nombre}
                                        />
                                    </div>
                                </form>
                            </Modal.Body>
                            <Modal.Footer>
                                <Button variant="secondary" onClick={handleCloseModal}>                    
                                    {t('objeto.txt_cerrar')}
                                </Button>
                                <Button variant="primary" onClick={handleSubmit}>
                                    {objeto.id==null?t('objeto.guardar'):t('objeto.actualizar')}
                                </Button>
                            </Modal.Footer>
                        </Modal>
                    </section>
                </div>
            </div>
        </div>        
    );
}

export default CatObjetosIndex;