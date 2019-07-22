import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MiscelanonpericolosaListComponent } from './miscelanonpericolosa-list.component';
import { MiscelanonpericolosaListRoutingModule } from './miscelanonpericolosa-list-routing.module';
import { FormsModule } from '@angular/forms';
import { SharedModule } from '../../shared/shared.module';


@NgModule({
  imports: [
    CommonModule,
    MiscelanonpericolosaListRoutingModule,
    FormsModule,
    SharedModule
  ],
  declarations: [
    MiscelanonpericolosaListComponent
  ]
})
export class MiscelanonpericolosaListModule { }
