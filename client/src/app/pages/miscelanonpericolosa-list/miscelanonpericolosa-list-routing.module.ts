import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { MiscelanonpericolosaListComponent } from './miscelanonpericolosa-list.component';

const routes: Routes = [
  {
    path: '',
    component: MiscelanonpericolosaListComponent
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class MiscelanonpericolosaListRoutingModule { }
