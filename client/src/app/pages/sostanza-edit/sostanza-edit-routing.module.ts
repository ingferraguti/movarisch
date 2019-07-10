import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { SostanzaEditComponent } from './sostanza-edit.component';

const routes: Routes = [
  {
    path: '',
    component: SostanzaEditComponent
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class SostanzaEditRoutingModule { }
