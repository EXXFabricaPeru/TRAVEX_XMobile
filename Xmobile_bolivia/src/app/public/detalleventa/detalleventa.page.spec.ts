import { CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { DetalleventaPage } from './detalleventa.page';

describe('DetalleventaPage', () => {
  let component: DetalleventaPage;
  let fixture: ComponentFixture<DetalleventaPage>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ DetalleventaPage ],
      schemas: [CUSTOM_ELEMENTS_SCHEMA],
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(DetalleventaPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
