SHELL := /usr/bin/env bash
.DEFAULT_GOAL := help

DC ?= docker compose
COMPOSE ?= $(DC) -f compose.yml

# Default service for exec/run helpers
SERVICE ?= maho

# Run exec commands as `maho` user when targeting maho service
ifeq ($(SERVICE),maho)
EXEC_USER := --user maho
else
EXEC_USER :=
endif

.PHONY: help
help:
	@echo "Usage: make <target>"
	@echo ""
	@echo "Docker Compose:"
	@echo "  up            Start stack (detached)"
	@echo "  down          Stop stack (and remove containers)"
	@echo "  stop          Stop services"
	@echo "  restart       Restart services"
	@echo "  build         Build images"
	@echo "  pull          Pull images"
	@echo "  ps            Show service status"
	@echo "  logs          Follow logs (optionally S=<service>)"
	@echo ""
	@echo "App helpers (default SERVICE=$(SERVICE)):"
	@echo "  sh            Shell into SERVICE (sh)"
	@echo "  bash          Shell into SERVICE (bash if present)"
	@echo "  maho          Run ./maho inside SERVICE (ARGS=...)"
	@echo "  composer      Run composer inside SERVICE (ARGS=...)"
	@echo "  cmd           Run arbitrary command inside SERVICE (CMD=...)"

.PHONY: up
up:
	$(COMPOSE) up -d --remove-orphans

.PHONY: down
down:
	$(COMPOSE) down --remove-orphans

.PHONY: stop
stop:
	$(COMPOSE) stop

.PHONY: restart
restart:
	$(COMPOSE) restart

.PHONY: build
build:
	$(COMPOSE) build

.PHONY: pull
pull:
	$(COMPOSE) pull

.PHONY: ps
ps:
	$(COMPOSE) ps

.PHONY: logs
logs:
	$(COMPOSE) logs -f --tail=200 $(S)

.PHONY: sh
sh:
	$(COMPOSE) exec $(EXEC_USER) $(SERVICE) sh

.PHONY: bash
bash:
	$(COMPOSE) exec $(EXEC_USER) $(SERVICE) bash

.PHONY: maho
maho:
	$(COMPOSE) exec $(EXEC_USER) $(SERVICE) sh -lc './maho $(ARGS)'

.PHONY: composer
composer:
	$(COMPOSE) exec $(EXEC_USER) $(SERVICE) sh -lc 'composer $(ARGS)'

.PHONY: cmd
cmd:
	@if [ -z "$(CMD)" ]; then echo "CMD is required, e.g. make cmd CMD='php -v'"; exit 2; fi
	$(COMPOSE) exec $(EXEC_USER) $(SERVICE) sh -lc '$(CMD)'
