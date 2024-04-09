import { useState, useEffect, useRef } from 'react';
import 'datatables.net-dt/css/jquery.dataTables.css';
import 'datatables.net';
import { Modal } from 'react-bootstrap';
import axios from 'axios';
import { toast } from 'react-toastify';
import i18n from '../util/lang/i18n';
import { useTranslation } from 'react-i18next';

function CatMenuIdiomaIndex() {
    const { t, i18n } = useTranslation();
    const tableRef = useRef(null);
    const [showModalTraduccion, setShowModalTraduccion] = useState(false);
    const [menuIdioma, setMenuIdioma] = useState({
        id: null,
        codigo: null,
        nombre: null,
        menu_id: null,
    });
    const [menuIdiomaData, setMenuIdiomaData] = useState(null);
    const [traducciones, setTraducciones] = useState([]);
    
    useEffect(() => {
        const table = $(tableRef.current).DataTable({
          ajax: {
            method: 'POST',
            url: './?action=cat_menu_get',
            data: { tipo: 1 }
          },
          columns: [
            { data: 'id' },
            { data: 'codigo' },
            { data: 'nombre' },
            { data: 'descripcion' },
            {
                data: null,
                render: function (data, type, row) {
                  return `
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-info btn-traduccion" title="Traducción" role="group"
                        data-id="${row.id}" data-nombre="${row.nombre}">
                        <i class="fas fa-book" aria-hidden="true"></i>
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
        setMenuIdiomaData(table);

        $(tableRef.current).on('click', '.btn-traduccion', function () {
            setMenuIdioma({
                id: $(this).data('id'),
                nombre: $(this).data('nombre'),
            })

            actionGetTraducctiones($(this).data('id'));
        });
    }, []);

    const handleInputChange = (men_id, idm_id, e) => {
        const { name, value } = e.target;
        const traducciones_ = traducciones.map((elemento) => {
          if (elemento.men_id === men_id && elemento.idm_id == idm_id) {
            return { ...elemento,[name]: value };
          }
          return elemento;
        });
    
        setTraducciones(traducciones_);
      };

    const formTraducciones = () => {
        return traducciones && traducciones.map((item, index) => (
            <form key={index}>
                <h5>{item.idm_nombre}</h5>
                <div className="form-group row">
                    <div className="col-md-6">
                        <label htmlFor="nombre">{t('menu_idioma.nombre')}</label>
                        <input type="text" className="form-control"
                            value={item.mnv_nombre}
                            name='mnv_nombre'
                            onChange={(e) => handleInputChange(item.men_id, item.idm_id, e)}/>
                    </div>
                    <div className="col-md-6">
                        <label htmlFor="codigo">{t('menu_idioma.descripcion')}</label>
                        <input type="text" className="form-control"
                            value={item.mnv_descripcion}
                            name='mnv_descripcion'
                            onChange={(e) => handleInputChange(item.men_id, item.idm_id, e)}/>
                    </div>
                </div>
                <div className='form-group'>
                    <button className="btn btn-sm btn-info" type="button"
                        onClick={() => actionSetTraduccion(item.men_id, item.idm_id, item.mnv_descripcion, item.mnv_nombre)}>Actualizar</button>
                </div>
                <hr></hr>
            </form>
        ))
    };

    useEffect(() => {
        if(traducciones.length > 0) {
            formTraducciones()
        }
    }, [traducciones]);

    const handleCloseModalTraduccion = () => {
        resetForm();
        setShowModalTraduccion(false);
    };    

    const resetForm = () => {
        setMenuIdioma({
            id: null,
            codigo: null,
            nombre: null,
            menu_id: null,
        });
    };

    const actionGetTraducctiones = async (men_id) => {
        const formData = new FormData();
        formData.append('men_id', men_id);

        axios.post(`?action=cat_menu_idioma_getForMenuId`, formData)
        .then((response) => {
            console.log(response.data);
            setShowModalTraduccion(true);
            setTraducciones(response.data.data)
        })
        .catch((error) => {
            console.error('Error al eliminar el menuIdioma:', error);
            toast.error('Ocurrió un error');
        });
    }

    const actionSetTraduccion = (men_id, idm_id, mnv_descripcion, mnv_nombre) => {
        const formData = new FormData();
        formData.append('men_id', men_id);
        formData.append('idm_id', idm_id);
        formData.append('mnv_descripcion', mnv_descripcion);
        formData.append('mnv_nombre', mnv_nombre);

        axios.post('./?action=cat_menu_data_set', formData)
        .then((response) => {
            let data = response.data
            if (response.data.substr(0, 1) == 1) {
                toast.success(data.substr(2));
            } else {
                toast.error(data.substr(2));
            }
        })
        .catch((error) => {
            console.error('Error al obtener los menuIdiomas:', error);
        });
    };

    return (
        <div className="row">
            <div className="col-12 grid-margin stretch-card">
                <div className="card">
                    <section>
                        <div className="well well-sm"
                            style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '0.5rem', margin: '0.5rem'}}
                            >
                            <h4>{t('menu_idioma.titulo')}</h4>
                        </div>
                        <div>
                            <div className="col-md-12">
                                <table className="display compact" style={{ width: '100%' }} id="table-entidades" ref={tableRef}>
                                    <thead>
                                        <tr>
                                            <th>{t('menu_idioma.id')}</th>
                                            <th>{t('menu_idioma.codigo')}</th>
                                            <th>{t('menu_idioma.nombre')}</th>
                                            <th>{t('menu_idioma.descripcion')}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <Modal show={showModalTraduccion} onHide={handleCloseModalTraduccion} dialogClassName="modal-lg">
                            <Modal.Header>
                                <Modal.Title>{t('menu_idioma.editar_traduccion')}: {menuIdioma.nombre}</Modal.Title>
                                <button type="button" className="btn-close custom-close-btn" onClick={handleCloseModalTraduccion}>
                                &times;
                                </button>
                            </Modal.Header>
                            <Modal.Body>
                                {formTraducciones()}
                            </Modal.Body>
                        </Modal>
                    </section>
                </div>
            </div>
        </div>
    );
}

export default CatMenuIdiomaIndex;