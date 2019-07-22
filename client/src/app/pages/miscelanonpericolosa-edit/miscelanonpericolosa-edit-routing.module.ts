import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { MiscelanonpericolosaEditComponent } from './miscelanonpericolosa-edit.component';

const routes: Routes = [
  {
    path: '',
    component: MiscelanonpericolosaEditComponent
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class MiscelanonpericolosaEditRoutingModule { }
