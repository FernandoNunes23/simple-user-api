# Simple-User-Api
## Pré-Requisitos
- Docker Compose 1.27.4 ou mais recente
- Docker 19.03.13 ou mais recente

## Rodando a aplicação
Para rodar a aplicação você deve acessar a pasta que contém os arquivos do sistema e subir com o docker-compose.
```bash
cd [my-app-name]
docker-compose up -d
```

# REST API

Abaixo seguem os endpoints disponíveis na API

## Listar todos os usuários

### Request

`GET /user`

    curl -i http://localhost:8090/user

## Adicionar novo usuário

### Request

`POST /user`

    curl -i -d 'name=Foo&second_name=Test&email=teste@email.com&phone=51999999999' http://localhost:8090/user

## Atualizar um usuário

### Request

`PUT /user/:id`

    curl -i -d 'name=Foo&second_name=Test' -X PUT http://localhost:8090/user/1
## Deletar um usuário

### Request

`DELETE /user`

    curl -i -d 'email=teste@email.com' -X DELETE http://localhost:8090/user

