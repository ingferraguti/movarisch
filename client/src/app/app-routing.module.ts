// DEPENDENCIES
import { NgModule } from '@angular/core';
import { CanActivate, RouterModule, Routes } from '@angular/router';

/* START MY VIEWS IMPORT */
// Do not edit this comment content, it will be overwritten in next Skaffolder generation
import { HomeComponent} from './pages/home/home.component';
import { FrasiHEditComponent} from './pages/frasi-hedit/frasi-hedit.component';
import { FrasiHListComponent} from './pages/frasi-hlist/frasi-hlist.component';
import { MiscelanonpericolosaEditComponent} from './pages/miscelanonpericolosa-edit/miscelanonpericolosa-edit.component';
import { MiscelanonpericolosaListComponent} from './pages/miscelanonpericolosa-list/miscelanonpericolosa-list.component';
import { ProcessoEditComponent} from './pages/processo-edit/processo-edit.component';
import { ProcessoListComponent} from './pages/processo-list/processo-list.component';
import { SostanzaEditComponent} from './pages/sostanza-edit/sostanza-edit.component';
import { SostanzaListComponent} from './pages/sostanza-list/sostanza-list.component';

/* END MY VIEWS IMPORT */

// SECURITY
import { LoginComponent } from './pages/login/login.component';
import { ManageUserEditComponent } from './security/manage-user/edit-user/manage-user-edit.component';
import { ManageUserListComponent } from './security/manage-user/list-user/manage-user-list.component';
import { ProfileComponent } from './security/profile/profile.component';
import { AuthGuard } from './security/auth.guard';

/**
 * WEB APP ROUTES
 */
const routes: Routes = [
    { path: '', redirectTo: '/home', pathMatch: 'full'  },

    /* START MY VIEWS */

    { path: 'frasihs/:id',  loadChildren: './pages/frasi-hedit/frasi-hedit.module#FrasiHEditModule' , canActivate: [AuthGuard] },
    { path: 'frasihs',  loadChildren: './pages/frasi-hlist/frasi-hlist.module#FrasiHListModule' , canActivate: [AuthGuard] },
    { path: 'home',  loadChildren: './pages/home/home.module#HomeModule' , canActivate: [AuthGuard] },
    { path: 'miscelanonpericolosas/:id',  loadChildren: './pages/miscelanonpericolosa-edit/miscelanonpericolosa-edit.module#MiscelanonpericolosaEditModule' , canActivate: [AuthGuard] },
    { path: 'miscelanonpericolosas',  loadChildren: './pages/miscelanonpericolosa-list/miscelanonpericolosa-list.module#MiscelanonpericolosaListModule' , canActivate: [AuthGuard] },
    { path: 'processos/:id',  loadChildren: './pages/processo-edit/processo-edit.module#ProcessoEditModule' , canActivate: [AuthGuard] },
    { path: 'processos',  loadChildren: './pages/processo-list/processo-list.module#ProcessoListModule' , canActivate: [AuthGuard] },
    { path: 'sostanzas/:id',  loadChildren: './pages/sostanza-edit/sostanza-edit.module#SostanzaEditModule' , canActivate: [AuthGuard] },
    { path: 'sostanzas',  loadChildren: './pages/sostanza-list/sostanza-list.module#SostanzaListModule' , canActivate: [AuthGuard] },

 /* END MY VIEWS */

    // SECURITY
    { path: 'manage-users',  loadChildren: './security/manage-user/list-user/manage-user-list.module#ManageUserListModule', canActivate: [AuthGuard], data: ['ADMIN']},
    { path: 'manage-users/:id',  loadChildren: './security/manage-user/edit-user/manage-user-edit.module#ManageUserEditModule', canActivate: [AuthGuard], data: ['ADMIN']},
    { path: 'profile',  loadChildren: './security/profile/profile.module#ProfileModule', canActivate: [AuthGuard] },
    { path: 'login', loadChildren: './pages/login/login.module#LoginModule'}
];

/**
 * ROUTING MODULE
 */
@NgModule({
    imports: [ RouterModule.forRoot(routes) ],
    exports: [ RouterModule ]
})

export class AppRoutingModule {}
