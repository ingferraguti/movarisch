import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { ProcessoEditComponent } from './processo-edit.component';

const routes: Routes = [
  {
    path: '',
    component: ProcessoEditComponent
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class ProcessoEditRoutingModule { }
