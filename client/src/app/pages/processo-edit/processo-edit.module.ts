import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ProcessoEditComponent } from './processo-edit.component';
import { ProcessoEditRoutingModule } from './processo-edit-routing.module';
import { FormsModule } from '@angular/forms';
import { SharedModule } from '../../shared/shared.module';


@NgModule({
  imports: [
    CommonModule,
    ProcessoEditRoutingModule,
    FormsModule,
    SharedModule
  ],
  declarations: [
    ProcessoEditComponent
  ]
})
export class ProcessoEditModule { }
