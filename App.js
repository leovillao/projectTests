import React from 'react';
import { createRoot } from 'react-dom/client'

import CatCategoriasIndex from './core/modules/index/components/cat_categorias';
import CatSubCategoriasIndex from './core/modules/index/components/cat_subcategorias';
import CatIdiomasIndex from './core/modules/index/components/cat_idiomas';
import CatObjetosIndex from './core/modules/index/components/cat_objetos';
import CatPaisesIndex from './core/modules/index/components/cat_paises';
import CatRangosIpIndex from './core/modules/index/components/cat_rangosip';
import CatMenuIdiomaIndex from './core/modules/index/components/cat_menu_idioma';
import CatUsuariosIndex from './core/modules/index/components/cat_usuarios';
import CatEmpresaIndex from './core/modules/index/components/cat_empresa';
import { ToastContainer } from 'react-toastify';
import "react-toastify/dist/ReactToastify.css";

const catCategoriasComponent = document.getElementById('cat-categorias-index');
const catSubCategoriasComponent = document.getElementById('cat-subcategorias-index');
const catIdiomasComponent = document.getElementById('cat-idiomas-index');
const catObjetosComponent = document.getElementById('cat-objetos-index');
const catPaisesComponent = document.getElementById('cat-paises-index');
const catRangosIpComponent = document.getElementById('cat-rangosip-index');
const catMenuIdiomaComponent = document.getElementById('cat-menu-idioma-index');
const catUsuariosComponent = document.getElementById('cat-usuarios-index');
const catEmpresaComponent = document.getElementById('cat-empresa-index');

if (catCategoriasComponent) {
    createRoot(catCategoriasComponent).render(<CatCategoriasIndex/>);
}
if (catSubCategoriasComponent) {
    createRoot(catSubCategoriasComponent).render(<CatSubCategoriasIndex/>);
}
if (catIdiomasComponent) {
    createRoot(catIdiomasComponent).render(<CatIdiomasIndex/>);
}
if (catObjetosComponent) {
    createRoot(catObjetosComponent).render(<CatObjetosIndex/>);
}
if (catPaisesComponent) {
    createRoot(catPaisesComponent).render(<CatPaisesIndex/>);
}
if (catRangosIpComponent) {
    createRoot(catRangosIpComponent).render(<CatRangosIpIndex/>);
}
if (catMenuIdiomaComponent) {
    createRoot(catMenuIdiomaComponent).render(<CatMenuIdiomaIndex/>);
}
if (catUsuariosComponent) {
    createRoot(catUsuariosComponent).render(<CatUsuariosIndex/>);
}
if (catEmpresaComponent) {
    const empId = catEmpresaComponent.getAttribute('data-emp_id');
    createRoot(catEmpresaComponent).render(<CatEmpresaIndex empId={empId} />);
}

//se definen componentes globales
createRoot(document.getElementById('root')).render(
    <>
        <ToastContainer autoClose={1200} pauseOnHover={false} />
    </>
);