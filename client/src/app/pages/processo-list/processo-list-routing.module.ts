import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { ProcessoListComponent } from './processo-list.component';

const routes: Routes = [
  {
    path: '',
    component: ProcessoListComponent
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class ProcessoListRoutingModule { }
