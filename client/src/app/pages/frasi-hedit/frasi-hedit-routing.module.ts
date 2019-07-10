import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { FrasiHEditComponent } from './frasi-hedit.component';

const routes: Routes = [
  {
    path: '',
    component: FrasiHEditComponent
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class FrasiHEditRoutingModule { }
