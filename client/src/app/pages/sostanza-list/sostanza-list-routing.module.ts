import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { SostanzaListComponent } from './sostanza-list.component';

const routes: Routes = [
  {
    path: '',
    component: SostanzaListComponent
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class SostanzaListRoutingModule { }
