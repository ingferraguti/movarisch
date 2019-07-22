import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MiscelanonpericolosaEditComponent } from './miscelanonpericolosa-edit.component';
import { MiscelanonpericolosaEditRoutingModule } from './miscelanonpericolosa-edit-routing.module';
import { FormsModule } from '@angular/forms';
import { SharedModule } from '../../shared/shared.module';


@NgModule({
  imports: [
    CommonModule,
    MiscelanonpericolosaEditRoutingModule,
    FormsModule,
    SharedModule
  ],
  declarations: [
    MiscelanonpericolosaEditComponent
  ]
})
export class MiscelanonpericolosaEditModule { }
