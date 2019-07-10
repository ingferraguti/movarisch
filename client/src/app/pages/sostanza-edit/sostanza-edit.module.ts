import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SostanzaEditComponent } from './sostanza-edit.component';
import { SostanzaEditRoutingModule } from './sostanza-edit-routing.module';
import { FormsModule } from '@angular/forms';
import { SharedModule } from '../../shared/shared.module';


@NgModule({
  imports: [
    CommonModule,
    SostanzaEditRoutingModule,
    FormsModule,
    SharedModule
  ],
  declarations: [
    SostanzaEditComponent
  ]
})
export class SostanzaEditModule { }
