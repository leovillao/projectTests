import { useState, useEffect, useRef } from 'react';
import 'datatables.net-dt/css/jquery.dataTables.css';
import 'datatables.net';
import { Button, Modal } from 'react-bootstrap';
import axios from 'axios';
import Swal from 'sweetalert2'
import { toast } from 'react-toastify';
import i18n from '../util/lang/i18n';
import { useTranslation } from 'react-i18next';
import Select from 'react-select'

function CatUsuariosIndex() {
    const { t, i18n } = useTranslation();
    const tableRef = useRef(null);
    const [showModal, setShowModal] = useState(false);
    const usuarioInit = {
        id: null,
        perfil: '',
        nombre: '',
        nombre_usuario: '',
        password: '',
        email: '',
        prefijo: '',
        celular: '',
        is_caduca_password: false,
        dias_caduca_password: null,
        is_acceso_x_dia: false,
        acceso_dias: [
            { dia: 'lunes', acceso: true },
            { dia: 'martes', acceso: true },
            { dia: 'miercoles', acceso: true },
            { dia: 'jueves', acceso: true },
            { dia: 'viernes', acceso: true },
            { dia: 'sabado', acceso: true },
            { dia: 'domingo', acceso: true }
        ],
        is_acceso_horas: false,
        acceso_hora_inicio: '',
        acceso_hora_fin: '',
        is_acceso_control_pais: false,
        estado: true,
        paises_permitidos: []
    }
    const [usuario, setUsuario] = useState(usuarioInit);
    const [subUsuarioData, setUsuarioData] = useState(null);
    const [paises, setPaises] = useState([]);
    const [optionsPaises, setOptionsPaises] = useState([]);
    const [title, setTitle] = useState ('')
    
    useEffect(() => {
        const table = $(tableRef.current).DataTable({
          ajax: {
            method: 'POST',
            url: './?action=cat_usuarios_get',
            data: { tipo: 1 }
          },
          columns: [
            { data: 'id' },
            { data: 'nombre' },
            { data: 'usuario_login' },
            { data: 'email' },
            {
                data: 'estado',
                render: function (data) {
                    return data=='1'?`<label class="badge badge-info">${t('usuario.activo')}</label>`:`<label class="badge badge-danger">${t('usuario.inactivo')}</label>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                  return `
                    <div class="btn-group" role="group">
                      <button class="btn btn-sm btn-success btn-edit" title="Editar" role="group"
                        data-id="${row.id}" data-usuario_login="${row.usuario_login}" data-nombre="${row.nombre}">
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
          }
        });
        setUsuarioData(table);

        $(tableRef.current).on('click', '.btn-edit', function () {
            setUsuario({
                id: $(this).data('id'),
                usuario_login: $(this).data('usuario_login'),
            })
            setTitle($(this).data('nombre'))
            actionGetUsuarioById($(this).data('id'))
        });

        $(tableRef.current).on('click', '.btn-eliminar', function (e) {
            e.preventDefault();
            const subcategoriaId = $(this).data('id');
            const subcategoriaNombre = $(this).data('nombre');
            modalEliminarSubcategoria(table, subcategoriaId, subcategoriaNombre)
        });        
    }, []);

    useEffect(() => {
        actionGetPaises();
    },[]);

    useEffect(() => {
        loadPaisesPermitidos();
    }, [paises]);

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
                actionDeleteUsuario(table, formData);
            }
        })
    }

    const handleChange = (e) => {
        const { id, value, type, checked } = e.target;

        const nuevoAccesoDias = usuario.acceso_dias.map((item) => {
            if (item.dia === id) {
              return { ...item, acceso: checked };
            }
            return item;
          });
       
        if(type == 'checkbox') {
            setUsuario({ ...usuario, [id]: checked, acceso_dias: nuevoAccesoDias  });
        }
        else {
            setUsuario({ ...usuario, [id]: value });
        }
    };   
    
    const handlePaisesPermitidosChange = (selectedOptions) => {
        setUsuario({
          ...usuario,
          paises_permitidos: selectedOptions,
        });
      };
      
    const handleOpenModal = () => {
        setTitle('');
        setShowModal(true);
    };

    const loadPaisesPermitidos = () => {
        const datosConvertidos = paises.map((item) => {
            return { value: item.id, label: item.nombre };
        });
        setOptionsPaises(datosConvertidos);
    }

    const handleCloseModal = () => {
        resetForm();
        setShowModal(false);
    };    

    const handleSubmit = (e) => {
        e.preventDefault();
        const formData = new FormData();

        let action = '';

        let usr_dias1_7 = usuario.acceso_dias.map((item) => (item.acceso ? 'S' : 'N'));
        usr_dias1_7 = usr_dias1_7.join('')
        formData.append('usr_perfil', usuario.perfil);
        formData.append('usr_nombre', usuario.nombre);
        formData.append('usr_user', usuario.nombre_usuario);
        formData.append('usr_psw', usuario.password);
        formData.append('usr_email', usuario.email);
        formData.append('usr_numcel', usuario.prefijo+'-'+usuario.celular);
        formData.append('usr_caducapsw', usuario.is_caduca_password?'S':'N');
        formData.append('usr_periodo', usuario.dias_caduca_password);
        formData.append('usr_accesoxdia', usuario.is_acceso_x_dia?'S':'N');
        formData.append('usr_dias1_7', usr_dias1_7);
        formData.append('usr_rangohorario', usuario.is_acceso_horas?'S':'N');
        formData.append('usr_rangodesde', usuario.acceso_hora_inicio);
        formData.append('usr_rangohasta', usuario.acceso_hora_fin);
        formData.append('usr_controlpais', usuario.is_acceso_control_pais?'S':'N');
        formData.append('usr_estado', usuario.estado?'1':'0');
        
        const paises_permitidos = usuario.paises_permitidos.map((option) => option.value);
        formData.append('usr_paisespermitidos', paises_permitidos.join(','));        

        if(usuario.id == null) {
            action = 'cat_usuarios_set';
        }
        else {
            action = 'cat_usuarios_update';
            formData.append('usr_id', usuario.id);
        }

        actionSetUpdateUsuario(action, formData)
    };

    const resetForm = () => {
        setUsuario(usuarioInit);
        setShowModal(false);
    };

    const actionGetPaises = () => {
        console.log('actionGetPaises')
        axios.get('./?action=cat_paises_get')
        .then((response) => {
            setPaises(response.data.data);
        })
        .catch((error) => {
            console.error('Error al obtener los paises:', error);
        });
    };

    const actionGetUsuarioById = async (usr_id) => {
        const formData = new FormData();
        formData.append('usr_id', usr_id);

        axios.post(`?action=cat_usuarios_getForId`, formData)
        .then((response) => {
            setShowModal(true);
            setUsuario(response.data.data)
        })
        .catch((error) => {
            toast.error('Ocurrió un error');
        });
    }

    const actionSetUpdateUsuario = (action, formData) => {
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
                if (subUsuarioData) {
                    subUsuarioData.ajax.reload(null, false);
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

    const actionDeleteUsuario = (table, formData) => {
        axios.post(`?action=cat_usuarios_delete`, formData, {
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
            console.error('Error al eliminar usuario:', error);
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
                            <h4>{t('usuario.titulo')}</h4>
                            <div>
                                <button
                                    role="button"
                                    className="btn btn-sm btn-primary"
                                    id="nuevaSubcategoria"
                                    onClick={handleOpenModal}
                                >
                                   {t('usuario.txt_nuevo')}
                                </button>
                            </div>
                        </div>
                        <div>
                            <div className="col-md-12">
                                <table className="display compact" style={{ width: '100%' }} id="table-entidades" ref={tableRef}>
                                    <thead>
                                        <tr>
                                            <th>{t('usuario.id')}</th>
                                            <th>{t('usuario.nombre')}</th>
                                            <th>{t('usuario.usuario_login')}</th>
                                            <th>{t('usuario.email')}</th>
                                            <th>{t('usuario.estado')}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <Modal show={showModal} onHide={handleCloseModal} size="lg">
                        <Modal.Header>
                            <Modal.Title>
                                {usuario.id==null?t('usuario.txt_nuevo'):t('usuario.txt_editar')}
                                <p className="card-description">{title}</p>
                            </Modal.Title>
                            <button type="button" className="btn-close custom-close-btn" onClick={handleCloseModal}>
                            &times;
                            </button>
                        </Modal.Header>
                            <Modal.Body>
                                <form autoComplete='off'>
                                    <div className="form-group row">
                                        <div className="col-md-6">
                                            <label htmlFor="perfil">{t('usuario.perfil')}</label>
                                            <select className="form-control form-control-sm" id="perfil" name="perfil" value={usuario.perfil} onChange={handleChange}>
                                                <option value="">{t('usuario.seleccione_perfil')}</option>
                                                <option value="1">{t('usuario.administrador')}</option>
                                                <option value="2">{t('usuario.operador')}</option>
                                                <option value="3">{t('usuario.invitado')}</option>
                                            </select>
                                        </div>
                                        <div className="col-md-6">
                                            <label htmlFor="nombre">{t('usuario.nombre')}</label>
                                            <input
                                                className="form-control form-control-sm"
                                                type="text"
                                                id="nombre"
                                                onChange={handleChange}
                                                value={usuario.nombre}
                                            />
                                        </div>
                                    </div>
                                    <div className="form-group row">
                                        <div className="col-md-6">
                                            <label htmlFor="nombre_usuario">{t('usuario.nombre_usuario')}</label>
                                            <input
                                                className="form-control form-control-sm"
                                                type="text"
                                                id="nombre_usuario"
                                                onChange={handleChange}
                                                value={usuario.nombre_usuario}
                                            />
                                        </div>
                                        <div className="col-md-6">
                                            <label htmlFor="password">{t('usuario.password')}</label>
                                            <input
                                                className="form-control form-control-sm"
                                                type="password"
                                                id="password"
                                                autoComplete="false"
                                                onChange={handleChange}
                                                value={usuario.password}
                                            />
                                            { usuario.id &&
                                            <code>{t('usuario.password_helper')}</code>
                                            }
                                        </div>
                                    </div>
                                    <div className="form-group row">
                                        <div className="col-md-6">
                                            <label htmlFor="email">{t('usuario.email')}</label>
                                            <input
                                                className="form-control form-control-sm"
                                                type="email"
                                                id="email"
                                                autoComplete="false"
                                                onChange={handleChange}
                                                value={usuario.email}
                                            />
                                        </div>
                                        <div className="col-md-6">
                                            <label htmlFor="celular">{t('usuario.celular')}</label>
                                            <div className="input-group">
                                                <div className="input-group-append">
                                                    <select className="form-control form-control-sm" id="prefijo" name="prefijo" value={usuario.prefijo} onChange={handleChange}>
                                                        <option value="">{t('usuario.seleccione_prefijo')}</option>
                                                        { paises && paises.map((item, index) =>
                                                            (<option key={index} value={item.prefijo}>{item.nombre} (+{item.prefijo})</option>
                                                        ))}
                                                    </select>
                                                </div>
                                                <input
                                                    className="form-control form-control-sm"
                                                    type="text"
                                                    id="celular"
                                                    onChange={handleChange}
                                                    value={usuario.celular}
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div className='form-group mb-0'>
                                        <p className="mb-2">{t('usuario.is_acceso_horas')}</p>
                                        <label className="toggle-switch toggle-switch-success">
                                            <input id="is_acceso_horas" type="checkbox" checked={usuario.is_acceso_horas} onChange={handleChange} />
                                            <span className="toggle-slider round"></span>
                                        </label>
                                    </div>
                                    { usuario.is_acceso_horas &&
                                        <div className="form-group row">
                                            <div className="col-md-6">
                                                <label htmlFor="acceso_hora_inicio">{t('usuario.acceso_hora_inicio')}</label>
                                                <input
                                                    className="form-control form-control-sm"
                                                    type="time"
                                                    id="acceso_hora_inicio"
                                                    onChange={handleChange}
                                                    value={usuario.acceso_hora_inicio}
                                                />
                                            </div>
                                            <div className="col-md-6">
                                                <label htmlFor="acceso_hora_fin">{t('usuario.acceso_hora_fin')}</label>
                                                <input
                                                    className="form-control form-control-sm"
                                                    type="time"
                                                    id="acceso_hora_fin"
                                                    onChange={handleChange}
                                                    value={usuario.acceso_hora_fin}
                                                />
                                            </div>
                                        </div>
                                    }
                                    <div className="form-group row mb-0">
                                        <div className="col-md-4">
                                            <p className="mb-2">{t('usuario.is_caduca_password')}</p>
                                            <label className="toggle-switch toggle-switch-success">
                                                <input id="is_caduca_password" type="checkbox" checked={usuario.is_caduca_password} onChange={handleChange} />
                                                <span className="toggle-slider round"></span>
                                            </label>
                                        </div>
                                        { usuario.is_caduca_password &&
                                        <div className="col-md-6">
                                            <div className="form-group">
                                                <label htmlFor="dias_caduca_password">{t('usuario.dias_caduca_password')}</label>
                                                <input
                                                    className="form-control form-control-sm"
                                                    type="number"
                                                    min="1"
                                                    id="dias_caduca_password"
                                                    autoComplete="false"
                                                    onChange={handleChange}
                                                    value={usuario.dias_caduca_password}
                                                />
                                            </div>
                                        </div>
                                        }
                                    </div>
                                    <div className='form-group mb-0'>
                                        <p className="mb-2">{t('usuario.is_acceso_x_dia')}</p>
                                        <label className="toggle-switch toggle-switch-success">
                                            <input id="is_acceso_x_dia" type="checkbox" checked={usuario.is_acceso_x_dia} onChange={handleChange} />
                                            <span className="toggle-slider round"></span>
                                        </label>
                                    </div>
                                    { usuario.is_acceso_x_dia &&
                                        <div className="input-group">
                                            {usuario.acceso_dias && usuario.acceso_dias.map((item, index) => (
                                            <div className="form-check form-check-primary mr-2" key={item.dia}>
                                                <label className="form-check-label">
                                                <input
                                                    id={item.dia}
                                                    type="checkbox"
                                                    className="form-check-input"
                                                    checked={item.acceso}
                                                    onChange={handleChange}
                                                />
                                                {t(`usuario.dia_${item.dia}`)}<i className="input-helper"></i>
                                                </label>
                                            </div>
                                            ))}
                                        </div>
                                    }
                                    <div className='form-group mb-0'>
                                        <p className="mb-2">{t('usuario.is_acceso_control_pais')}</p>
                                        <label className="toggle-switch toggle-switch-success">
                                            <input id="is_acceso_control_pais" type="checkbox" checked={usuario.is_acceso_control_pais} onChange={handleChange} />
                                            <span className="toggle-slider round"></span>
                                        </label>
                                    </div>
                                    { usuario.is_acceso_control_pais && 
                                        <Select options={optionsPaises} placeholder="Seleccione los paises permitidos"
                                            isMulti
                                            value={usuario.paises_permitidos}
                                            onChange={handlePaisesPermitidosChange}/>
                                    }
                                    <div className='form-group mb-0 mt-2'>
                                        <p className="mb-2">{t('usuario.estado')}</p>
                                        <label className="toggle-switch toggle-switch-success">
                                            <input id="estado" type="checkbox"
                                                checked={usuario.estado}
                                                onChange={handleChange} />
                                            <span className="toggle-slider round"></span>
                                        </label>
                                    </div>
                                </form>
                            </Modal.Body>
                            <Modal.Footer>
                                <Button variant="secondary" onClick={handleCloseModal}>
                                    {t('usuario.cancelar')}
                                </Button>
                                <Button variant="primary" onClick={handleSubmit}>
                                    {usuario.id==null?t('usuario.guardar'):t('usuario.actualizar')}
                                </Button>
                            </Modal.Footer>
                        </Modal>
                    </section>
                </div>
            </div>
        </div>        
    );
}

export default CatUsuariosIndex;