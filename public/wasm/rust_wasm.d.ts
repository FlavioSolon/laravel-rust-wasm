/* tslint:disable */
/* eslint-disable */
export class Particle {
  private constructor();
  free(): void;
  x: number;
  y: number;
  z: number;
  vx: number;
  vy: number;
  vz: number;
  radius: number;
}
export class ParticleSystem {
  private constructor();
  free(): void;
  static new(count: number, width: number, height: number, max_speed: number, radius: number): ParticleSystem;
  update(width: number, height: number, depth: number, collision_enabled: boolean): void;
  get_particles(): Particle[];
}

export type InitInput = RequestInfo | URL | Response | BufferSource | WebAssembly.Module;

export interface InitOutput {
  readonly memory: WebAssembly.Memory;
  readonly __wbg_particlesystem_free: (a: number, b: number) => void;
  readonly __wbg_particle_free: (a: number, b: number) => void;
  readonly __wbg_get_particle_x: (a: number) => number;
  readonly __wbg_set_particle_x: (a: number, b: number) => void;
  readonly __wbg_get_particle_y: (a: number) => number;
  readonly __wbg_set_particle_y: (a: number, b: number) => void;
  readonly __wbg_get_particle_z: (a: number) => number;
  readonly __wbg_set_particle_z: (a: number, b: number) => void;
  readonly __wbg_get_particle_vx: (a: number) => number;
  readonly __wbg_set_particle_vx: (a: number, b: number) => void;
  readonly __wbg_get_particle_vy: (a: number) => number;
  readonly __wbg_set_particle_vy: (a: number, b: number) => void;
  readonly __wbg_get_particle_vz: (a: number) => number;
  readonly __wbg_set_particle_vz: (a: number, b: number) => void;
  readonly __wbg_get_particle_radius: (a: number) => number;
  readonly __wbg_set_particle_radius: (a: number, b: number) => void;
  readonly particlesystem_new: (a: number, b: number, c: number, d: number, e: number) => number;
  readonly particlesystem_update: (a: number, b: number, c: number, d: number, e: number) => void;
  readonly particlesystem_get_particles: (a: number) => [number, number];
  readonly __wbindgen_export_0: WebAssembly.Table;
  readonly __externref_drop_slice: (a: number, b: number) => void;
  readonly __wbindgen_free: (a: number, b: number, c: number) => void;
  readonly __wbindgen_start: () => void;
}

export type SyncInitInput = BufferSource | WebAssembly.Module;
/**
* Instantiates the given `module`, which can either be bytes or
* a precompiled `WebAssembly.Module`.
*
* @param {{ module: SyncInitInput }} module - Passing `SyncInitInput` directly is deprecated.
*
* @returns {InitOutput}
*/
export function initSync(module: { module: SyncInitInput } | SyncInitInput): InitOutput;

/**
* If `module_or_path` is {RequestInfo} or {URL}, makes a request and
* for everything else, calls `WebAssembly.instantiate` directly.
*
* @param {{ module_or_path: InitInput | Promise<InitInput> }} module_or_path - Passing `InitInput` directly is deprecated.
*
* @returns {Promise<InitOutput>}
*/
export default function __wbg_init (module_or_path?: { module_or_path: InitInput | Promise<InitInput> } | InitInput | Promise<InitInput>): Promise<InitOutput>;
