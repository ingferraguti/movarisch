import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { FrasiHListComponent } from './frasi-hlist.component';

const routes: Routes = [
  {
    path: '',
    component: FrasiHListComponent
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class FrasiHListRoutingModule { }
